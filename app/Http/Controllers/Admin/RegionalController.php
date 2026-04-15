<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Regional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegionalController extends Controller
{
    public function index(): View
    {
        $regionais = Regional::query()->orderBy('nome')->get();

        return view('admin.regionais.index', [
            'regionais' => $regionais,
        ]);
    }

    public function create(): View
    {
        return view('admin.regionais.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'pastor_responsavel' => ['required', 'string', 'max:255'],
        ]);

        Regional::query()->create($validated);

        return redirect()
            ->route('admin.regionais.index')
            ->with('success', 'Regional cadastrada com sucesso.');
    }

    public function edit(Regional $regional): View
    {
        return view('admin.regionais.edit', [
            'regional' => $regional,
        ]);
    }

    public function update(Request $request, Regional $regional): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'pastor_responsavel' => ['required', 'string', 'max:255'],
        ]);

        $regional->update($validated);

        return redirect()
            ->route('admin.regionais.index')
            ->with('success', 'Regional atualizada com sucesso.');
    }

    public function destroy(Regional $regional): RedirectResponse
    {
        if ($regional->igrejas()->exists()) {
            return redirect()
                ->route('admin.regionais.index')
                ->with('error', 'Não é possível excluir: existem igrejas vinculadas a esta regional.');
        }

        $regional->delete();

        return redirect()
            ->route('admin.regionais.index')
            ->with('success', 'Regional excluída com sucesso.');
    }
}
