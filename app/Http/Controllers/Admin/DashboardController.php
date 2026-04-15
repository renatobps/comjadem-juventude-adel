<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Igreja;
use App\Models\PreInscricao;
use App\Models\Regional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $regionalScopeIds = $request->user()?->regionalScopeIds() ?? [];
        $validated = $request->validate([
            'regional_id' => ['nullable', 'integer', 'exists:regionais,id'],
            'igreja_id' => ['nullable', 'integer', 'exists:igrejas,id'],
            'per_page' => ['nullable', 'integer', 'in:10,50,100'],
        ]);

        $requestedRegionalId = isset($validated['regional_id']) ? (int) $validated['regional_id'] : null;
        $selectedRegionalId = $requestedRegionalId;
        if (! empty($regionalScopeIds)) {
            $selectedRegionalId = $requestedRegionalId && in_array($requestedRegionalId, $regionalScopeIds, true)
                ? $requestedRegionalId
                : null;
        }
        $selectedIgrejaId = $validated['igreja_id'] ?? null;
        $perPage = $validated['per_page'] ?? 10;

        $query = PreInscricao::query()
            ->with(['igrejaRel.regional'])
            ->orderByDesc('created_at');

        if (! empty($regionalScopeIds)) {
            if ($selectedRegionalId) {
                $query->whereHas('igrejaRel', function ($q) use ($selectedRegionalId): void {
                    $q->where('regional_id', $selectedRegionalId);
                });
            } else {
                $query->whereHas('igrejaRel', function ($q) use ($regionalScopeIds): void {
                    $q->whereIn('regional_id', $regionalScopeIds);
                });
            }
        } elseif ($selectedRegionalId) {
            $query->whereHas('igrejaRel', function ($q) use ($selectedRegionalId): void {
                $q->where('regional_id', $selectedRegionalId);
            });
        }

        if ($selectedIgrejaId) {
            $query->where('igreja_id', $selectedIgrejaId);
        }

        $inscricoes = $query
            ->paginate($perPage)
            ->withQueryString();

        $regionaisFiltro = Regional::query()
            ->withCount('igrejas')
            ->when(! empty($regionalScopeIds), fn ($q) => $q->whereIn('id', $regionalScopeIds))
            ->orderBy('nome')
            ->get();
        $igrejasFiltro = Igreja::query()
            ->when(! empty($regionalScopeIds), fn ($q) => $q->whereIn('regional_id', $regionalScopeIds))
            ->when($selectedRegionalId, fn ($q) => $q->where('regional_id', $selectedRegionalId))
            ->orderBy('bairro')
            ->get();
        $inscricoesPorRegionalQuery = PreInscricao::query()
            ->selectRaw('igrejas.regional_id, COUNT(*) as total')
            ->join('igrejas', 'pre_inscricoes.igreja_id', '=', 'igrejas.id')
            ->whereNotNull('igrejas.regional_id');
        if (! empty($regionalScopeIds)) {
            $inscricoesPorRegionalQuery->whereIn('igrejas.regional_id', $regionalScopeIds);
        }
        $inscricoesPorRegional = $inscricoesPorRegionalQuery
            ->groupBy('igrejas.regional_id')
            ->pluck('total', 'igrejas.regional_id')
            ->mapWithKeys(fn ($total, $regionalId) => [(int) $regionalId => (int) $total]);
        $regionaisCards = $regionaisFiltro->map(function (Regional $regional) use ($inscricoesPorRegional) {
            return [
                'regional' => $regional,
                'total' => (int) ($inscricoesPorRegional[$regional->id] ?? 0),
            ];
        });
        $metaConfig = DB::table('inscricao_meta_configuracoes')->first();
        $metaInscricoes = (int) ($metaConfig->meta_total ?? 500);
        $metaPorRegional = DB::table('inscricao_meta_regionais')
            ->pluck('meta', 'regional_id')
            ->mapWithKeys(fn ($meta, $regionalId) => [(int) $regionalId => (int) $meta]);
        $totalInscricoes = (clone $query)->toBase()->getCountForPagination();
        $percentualMeta = $metaInscricoes > 0
            ? min(100, (int) round(($totalInscricoes / $metaInscricoes) * 100))
            : 0;
        $regionaisComIgrejas = $regionaisFiltro
            ->filter(fn (Regional $regional) => (int) $regional->igrejas_count > 0)
            ->values();
        $totalIgrejas = (int) $regionaisComIgrejas->sum('igrejas_count');
        $metasRegionais = collect();

        if ($totalIgrejas > 0 && $metaInscricoes > 0) {
            $base = $regionaisComIgrejas->map(function (Regional $regional) use ($metaInscricoes, $totalIgrejas, $inscricoesPorRegional) {
                $raw = ($regional->igrejas_count / $totalIgrejas) * $metaInscricoes;
                $floor = (int) floor($raw);
                $inscricoesAtual = (int) $inscricoesPorRegional->get((int) $regional->id, 0);

                return [
                    'regional' => $regional,
                    'igrejas' => (int) $regional->igrejas_count,
                    'meta' => $floor,
                    'remainder' => $raw - $floor,
                    'inscricoes_atual' => $inscricoesAtual,
                ];
            })->values();

            $baseArray = $base->all();
            foreach ($baseArray as $index => $item) {
                $regionalId = (int) $item['regional']->id;
                if ($metaPorRegional->has($regionalId)) {
                    $baseArray[$index]['meta'] = (int) $metaPorRegional->get($regionalId);
                }
            }
            $base = collect($baseArray);

            $temMetaManual = $base->contains(fn (array $item) => $metaPorRegional->has((int) $item['regional']->id));
            $faltantes = $metaInscricoes - $base->sum('meta');
            if (! $temMetaManual && $faltantes > 0) {
                $indices = $base
                    ->sortByDesc('remainder')
                    ->take($faltantes)
                    ->keys()
                    ->all();

                $baseArray = $base->all();
                foreach ($indices as $index) {
                    if (isset($baseArray[$index])) {
                        $baseArray[$index]['meta']++;
                    }
                }
                $base = collect($baseArray);
            }

            $metasRegionais = $base
                ->map(function (array $item) {
                    $meta = (int) $item['meta'];
                    $atual = (int) $item['inscricoes_atual'];
                    $item['percentual'] = $meta > 0
                        ? min(100, (int) round(($atual / $meta) * 100))
                        : 0;

                    return $item;
                })
                ->sortBy(fn (array $item) => $item['regional']->nome)
                ->values();
        }

        return view('admin.dashboard', [
            'inscricoes' => $inscricoes,
            'total' => $totalInscricoes,
            'regionaisFiltro' => $regionaisFiltro,
            'igrejasFiltro' => $igrejasFiltro,
            'selectedRegionalId' => $selectedRegionalId,
            'selectedIgrejaId' => $selectedIgrejaId,
            'selectedPerPage' => $perPage,
            'regionaisCards' => $regionaisCards,
            'metaInscricoes' => $metaInscricoes,
            'percentualMeta' => $percentualMeta,
            'metasRegionais' => $metasRegionais,
            'totalIgrejasMeta' => $totalIgrejas,
        ]);
    }
}
