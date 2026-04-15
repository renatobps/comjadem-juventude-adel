<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Igreja;
use App\Models\Membro;
use App\Models\Regional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IgrejaController extends Controller
{
    public function index(Request $request): View
    {
        $regionalScopeIds = $request->user()?->regionalScopeIds() ?? [];

        $igrejas = Igreja::query()
            ->with(['regional', 'dirigenteMembro'])
            ->when(! empty($regionalScopeIds), fn ($q) => $q->whereIn('regional_id', $regionalScopeIds))
            ->orderBy('bairro')
            ->get();

        $regionais = Regional::query()
            ->withCount('igrejas')
            ->when(! empty($regionalScopeIds), fn ($q) => $q->whereIn('id', $regionalScopeIds))
            ->orderBy('nome')
            ->get();

        return view('admin.igrejas.index', [
            'igrejas' => $igrejas,
            'regionais' => $regionais,
            'regionaisCards' => $regionais->take(5)->values(),
        ]);
    }

    public function create(Request $request): View
    {
        $regionalScopeIds = $request->user()?->regionalScopeIds() ?? [];
        $regionais = Regional::query()
            ->when(! empty($regionalScopeIds), fn ($q) => $q->whereIn('id', $regionalScopeIds))
            ->orderBy('nome')
            ->get();
        $membros = Membro::query()->orderBy('nome')->get();

        return view('admin.igrejas.create', [
            'regionais' => $regionais,
            'membros' => $membros,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $regionalScopeIds = $request->user()?->regionalScopeIds() ?? [];
        $validated = $request->validate([
            'bairro' => ['required', 'string', 'max:255'],
            'dirigente_membro_id' => ['required', 'exists:membros,id'],
            'regional_id' => ['required', 'exists:regionais,id'],
        ]);

        if (! empty($regionalScopeIds) && ! in_array((int) $validated['regional_id'], $regionalScopeIds, true)) {
            abort(403, 'Você não pode cadastrar igreja em outra regional.');
        }

        $membro = Membro::query()->findOrFail($validated['dirigente_membro_id']);
        $validated['dirigente'] = $membro->nome;

        Igreja::query()->create($validated);

        return redirect()
            ->route('admin.igrejas.index')
            ->with('success', 'Igreja cadastrada com sucesso.');
    }

    public function edit(Request $request, Igreja $igreja): View
    {
        $regionalScopeIds = $request->user()?->regionalScopeIds() ?? [];
        if (! empty($regionalScopeIds) && ! in_array((int) $igreja->regional_id, $regionalScopeIds, true)) {
            abort(403, 'Você não pode acessar igrejas de outra regional.');
        }

        $regionais = Regional::query()
            ->when(! empty($regionalScopeIds), fn ($q) => $q->whereIn('id', $regionalScopeIds))
            ->orderBy('nome')
            ->get();
        $membros = Membro::query()->orderBy('nome')->get();

        return view('admin.igrejas.edit', [
            'igreja' => $igreja,
            'regionais' => $regionais,
            'membros' => $membros,
        ]);
    }

    public function update(Request $request, Igreja $igreja): RedirectResponse
    {
        $regionalScopeIds = $request->user()?->regionalScopeIds() ?? [];
        if (! empty($regionalScopeIds) && ! in_array((int) $igreja->regional_id, $regionalScopeIds, true)) {
            abort(403, 'Você não pode alterar igrejas de outra regional.');
        }

        $validated = $request->validate([
            'bairro' => ['required', 'string', 'max:255'],
            'dirigente_membro_id' => ['required', 'exists:membros,id'],
            'regional_id' => ['required', 'exists:regionais,id'],
        ]);

        if (! empty($regionalScopeIds) && ! in_array((int) $validated['regional_id'], $regionalScopeIds, true)) {
            abort(403, 'Você não pode mover igreja para outra regional sem acesso.');
        }

        $membro = Membro::query()->findOrFail($validated['dirigente_membro_id']);
        $validated['dirigente'] = $membro->nome;

        $igreja->update($validated);

        return redirect()
            ->route('admin.igrejas.index')
            ->with('success', 'Igreja atualizada com sucesso.');
    }

    public function destroy(Request $request, Igreja $igreja): RedirectResponse
    {
        $regionalScopeIds = $request->user()?->regionalScopeIds() ?? [];
        if (! empty($regionalScopeIds) && ! in_array((int) $igreja->regional_id, $regionalScopeIds, true)) {
            abort(403, 'Você não pode excluir igrejas de outra regional.');
        }

        $igreja->delete();

        return redirect()
            ->route('admin.igrejas.index')
            ->with('success', 'Igreja excluída com sucesso.');
    }
}
