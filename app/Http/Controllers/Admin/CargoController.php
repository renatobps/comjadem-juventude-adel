<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CargoController extends Controller
{
    public function index(): View
    {
        return view('admin.cargos.index', [
            'cargos' => Cargo::query()->orderBy('nome')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.cargos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:cargos,nome'],
        ]);

        Cargo::query()->create($validated);

        return redirect()->route('admin.cargos.index')
            ->with('success', 'Cargo cadastrado com sucesso.');
    }

    public function edit(Cargo $cargo): View
    {
        return view('admin.cargos.edit', [
            'cargo' => $cargo,
        ]);
    }

    public function update(Request $request, Cargo $cargo): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:cargos,nome,'.$cargo->id],
        ]);

        $cargo->update($validated);

        return redirect()->route('admin.cargos.index')
            ->with('success', 'Cargo atualizado com sucesso.');
    }

    public function destroy(Cargo $cargo): RedirectResponse
    {
        if ($cargo->membros()->exists()) {
            return redirect()->route('admin.cargos.index')
                ->with('error', 'Não é possível excluir este cargo, pois existem membros vinculados.');
        }

        $cargo->delete();

        return redirect()->route('admin.cargos.index')
            ->with('success', 'Cargo excluído com sucesso.');
    }
}
