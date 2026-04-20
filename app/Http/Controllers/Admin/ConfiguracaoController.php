<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membro;
use App\Models\MembroAcessoRegional;
use App\Models\Regional;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ConfiguracaoController extends Controller
{
    public function index(): View
    {
        $metaConfig = DB::table('inscricao_meta_configuracoes')->first();
        $metasRegionais = DB::table('inscricao_meta_regionais')
            ->pluck('meta', 'regional_id');

        return view('admin.configuracoes.index', [
            'membros' => Membro::query()
                ->with(['cargo', 'acessosRegionais.regional'])
                ->orderBy('nome')
                ->get(),
            'regionais' => Regional::query()->orderBy('nome')->get(),
            'metaTotal' => (int) ($metaConfig->meta_total ?? 500),
            'valorInscricao' => (float) ($metaConfig->valor_inscricao ?? 0),
            'metasRegionais' => $metasRegionais,
            'admins' => User::query()->where('is_admin', true)->orderBy('name')->get(),
        ]);
    }

    public function atribuirAcesso(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'membro_id' => ['required', 'integer', 'exists:membros,id'],
            'regional_ids' => ['required', 'array', 'min:1'],
            'regional_ids.*' => ['required', 'integer', 'exists:regionais,id'],
        ]);

        $membro = Membro::query()->findOrFail($validated['membro_id']);
        if (blank($membro->email) || blank($membro->senha)) {
            return back()->withErrors([
                'membro_id' => 'O membro precisa ter e-mail e senha preenchidos para receber acesso.',
            ])->withInput();
        }

        User::query()->firstOrCreate(
            ['email' => $membro->email],
            [
                'name' => $membro->nome,
                'password' => $membro->senha,
                'is_admin' => false,
            ]
        );

        MembroAcessoRegional::query()->where('membro_id', $membro->id)->delete();

        foreach (array_unique($validated['regional_ids']) as $regionalId) {
            MembroAcessoRegional::query()->create([
                'membro_id' => $membro->id,
                'regional_id' => (int) $regionalId,
            ]);
        }

        return redirect()
            ->route('admin.configuracoes.index')
            ->with('success', 'Acessos regionais atribuídos com sucesso.');
    }

    public function salvarMetas(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'meta_total' => ['required', 'integer', 'min:1'],
            'valor_inscricao' => ['required', 'numeric', 'min:0'],
            'metas_regionais' => ['required', 'array'],
            'metas_regionais.*' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated): void {
            $configAtual = DB::table('inscricao_meta_configuracoes')->first();
            if ($configAtual) {
                DB::table('inscricao_meta_configuracoes')
                    ->where('id', $configAtual->id)
                    ->update([
                        'meta_total' => (int) $validated['meta_total'],
                        'valor_inscricao' => round((float) $validated['valor_inscricao'], 2),
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('inscricao_meta_configuracoes')->insert([
                    'meta_total' => (int) $validated['meta_total'],
                    'valor_inscricao' => round((float) $validated['valor_inscricao'], 2),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($validated['metas_regionais'] as $regionalId => $meta) {
                DB::table('inscricao_meta_regionais')->updateOrInsert(
                    ['regional_id' => (int) $regionalId],
                    [
                        'meta' => (int) ($meta ?? 0),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });

        return redirect()
            ->route('admin.configuracoes.index')
            ->with('success', 'Metas de inscrições salvas com sucesso.');
    }

    public function atribuirAdministrador(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'membro_id_admin' => ['required', 'integer', 'exists:membros,id'],
        ]);

        $membro = Membro::query()->findOrFail($validated['membro_id_admin']);
        if (blank($membro->email) || blank($membro->senha)) {
            return back()->withErrors([
                'membro_id_admin' => 'O membro precisa ter e-mail e senha preenchidos para ser administrador.',
            ])->withInput();
        }

        User::query()->updateOrCreate(
            ['email' => $membro->email],
            [
                'name' => $membro->nome,
                'password' => $membro->senha,
                'is_admin' => true,
            ]
        );

        return redirect()
            ->route('admin.configuracoes.index')
            ->with('success', 'Usuário promovido a administrador com acesso total.');
    }
}
