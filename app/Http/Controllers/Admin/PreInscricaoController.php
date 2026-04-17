<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Igreja;
use App\Models\PreInscricao;
use App\Support\NotificacaoHistorico;
use App\Support\NotificacaoPosInscricaoConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PreInscricaoController extends Controller
{
    public function edit(Request $request, PreInscricao $pre_inscricao): View
    {
        $regionalScopeIds = $request->user()?->regionalScopeIds() ?? [];
        $this->assertRegionalAccess($pre_inscricao, $regionalScopeIds);

        return view('admin.pre-inscricoes.edit', [
            'inscricao' => $pre_inscricao,
            'igrejas' => Igreja::query()
                ->when(! empty($regionalScopeIds), fn ($q) => $q->whereIn('regional_id', $regionalScopeIds))
                ->orderBy('bairro')
                ->get(),
            'statusOptions' => PreInscricao::statusOptions(),
        ]);
    }

    public function update(Request $request, PreInscricao $pre_inscricao): RedirectResponse
    {
        $regionalScopeIds = $request->user()?->regionalScopeIds() ?? [];
        $this->assertRegionalAccess($pre_inscricao, $regionalScopeIds);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'idade' => ['required', 'integer', 'min:10', 'max:120'],
            'whatsapp' => ['required', 'string', 'max:40'],
            'tamanho_camiseta' => ['required', 'string', 'in:P,M,G,GG,XG'],
            'igreja_id' => [
                'nullable',
                'integer',
                Rule::exists('igrejas', 'id')->when(
                    ! empty($regionalScopeIds),
                    fn ($rule) => $rule->whereIn('regional_id', $regionalScopeIds),
                ),
            ],
            'lider_jovens' => ['required', 'boolean'],
            'status' => ['required', 'string', 'in:aguardando,confirmada,cancelada'],
        ]);

        $igrejaNome = $pre_inscricao->igreja;

        if (! empty($validated['igreja_id'])) {
            $igreja = Igreja::query()->findOrFail($validated['igreja_id']);
            $igrejaNome = $igreja->nomeNoFormulario();
        }

        $statusAnterior = $pre_inscricao->status;
        $pre_inscricao->update([
            'nome' => $validated['nome'],
            'idade' => $validated['idade'],
            'whatsapp' => $validated['whatsapp'],
            'tamanho_camiseta' => $validated['tamanho_camiseta'],
            'igreja_id' => $validated['igreja_id'] ?? null,
            'igreja' => $igrejaNome,
            'lider_jovens' => (bool) $validated['lider_jovens'],
            'status' => $validated['status'],
        ]);

        if ($statusAnterior !== PreInscricao::STATUS_CONFIRMADA && $pre_inscricao->status === PreInscricao::STATUS_CONFIRMADA) {
            $this->enviarNotificacaoConfirmada($pre_inscricao);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Pré-inscrição atualizada com sucesso.');
    }

    public function destroy(PreInscricao $pre_inscricao): RedirectResponse
    {
        $regionalScopeIds = request()->user()?->regionalScopeIds() ?? [];
        $this->assertRegionalAccess($pre_inscricao, $regionalScopeIds);

        $pre_inscricao->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Pré-inscrição excluída com sucesso.');
    }

    private function assertRegionalAccess(PreInscricao $preInscricao, array $regionalScopeIds): void
    {
        if (empty($regionalScopeIds)) {
            return;
        }

        $allowed = PreInscricao::query()
            ->whereKey($preInscricao->id)
            ->whereHas('igrejaRel', fn ($q) => $q->whereIn('regional_id', $regionalScopeIds))
            ->exists();

        if (! $allowed) {
            abort(403, 'Você não pode acessar inscrições de outra regional.');
        }
    }

    private function enviarNotificacaoConfirmada(PreInscricao $inscricao): void
    {
        $baseUrl = (string) config('services.evolution_go.base_url');
        $instanceToken = (string) config('services.evolution_go.instance_token');
        $apiKey = (string) config('services.evolution_go.api_key');

        if ($baseUrl === '' || ($instanceToken === '' && $apiKey === '')) {
            return;
        }

        $numero = $this->normalizarNumeroWhatsapp((string) $inscricao->whatsapp);
        if ($numero === null) {
            return;
        }

        $mensagemTemplate = NotificacaoPosInscricaoConfig::mensagemConfirmada();
        $mensagem = strtr($mensagemTemplate, [
            '{nome_do_inscrito}' => (string) $inscricao->nome,
            '{tamanho_camiseta}' => (string) $inscricao->tamanho_camiseta,
        ]);

        $payload = [
            'number' => $numero,
            'text' => $mensagem,
            'delay' => 500,
        ];

        $apiKeyHeader = $instanceToken !== '' ? $instanceToken : $apiKey;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'apikey' => $apiKeyHeader,
            ])->timeout(20)->post(rtrim($baseUrl, '/') . '/send/text', $payload);

            if ($response->failed()) {
                NotificacaoHistorico::registrar($numero, $mensagem, 'erro');
                Log::warning('Falha ao enviar notificação de confirmação (edição).', [
                    'pre_inscricao_id' => $inscricao->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return;
            }

            NotificacaoHistorico::registrar($numero, $mensagem, 'enviada');
        } catch (\Throwable $e) {
            NotificacaoHistorico::registrar($numero, $mensagem, 'erro');
            Log::warning('Exceção ao enviar notificação de confirmação (edição).', [
                'pre_inscricao_id' => $inscricao->id,
                'erro' => $e->getMessage(),
            ]);
        }
    }

    private function normalizarNumeroWhatsapp(string $rawNumber): ?string
    {
        $digits = preg_replace('/\D+/', '', $rawNumber) ?: '';

        if (strlen($digits) === 11) {
            return '55' . $digits;
        }

        if (strlen($digits) === 13 && str_starts_with($digits, '55')) {
            return $digits;
        }

        return null;
    }
}
