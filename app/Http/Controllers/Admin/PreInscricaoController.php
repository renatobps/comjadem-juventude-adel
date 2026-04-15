<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Igreja;
use App\Models\PreInscricao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $pre_inscricao->update([
            'nome' => $validated['nome'],
            'idade' => $validated['idade'],
            'whatsapp' => $validated['whatsapp'],
            'igreja_id' => $validated['igreja_id'] ?? null,
            'igreja' => $igrejaNome,
            'lider_jovens' => (bool) $validated['lider_jovens'],
            'status' => $validated['status'],
        ]);

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
}
