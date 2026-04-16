<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\Membro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MembroController extends Controller
{
    public function index(): View
    {
        return view('admin.membros.index', [
            'membros' => Membro::query()->with('cargo')->orderBy('nome')->get(),
            'cargos' => Cargo::query()->orderBy('nome')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.membros.create', [
            'cargos' => Cargo::query()->orderBy('nome')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:membros,email'],
            'senha' => ['required', 'string', 'min:6', 'max:255'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'cargo_id' => ['nullable', 'exists:cargos,id'],
            'telefone' => ['required', 'string', 'max:40'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('membros/fotos', 'public');
        }

        Membro::query()->create($validated);

        return redirect()->route('admin.membros.index')
            ->with('success', 'Membro cadastrado com sucesso.');
    }

    public function edit(Membro $membro): View
    {
        return view('admin.membros.edit', [
            'membro' => $membro,
            'cargos' => Cargo::query()->orderBy('nome')->get(),
        ]);
    }

    public function update(Request $request, Membro $membro): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:membros,email,' . $membro->id],
            'senha' => ['nullable', 'string', 'min:6', 'max:255'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'cargo_id' => ['nullable', 'exists:cargos,id'],
            'telefone' => ['nullable', 'string', 'max:40'],
        ]);

        if (! array_key_exists('senha', $validated) || $validated['senha'] === null) {
            unset($validated['senha']);
        }

        if ($request->hasFile('foto')) {
            if ($membro->foto) {
                Storage::disk('public')->delete($membro->foto);
            }
            $validated['foto'] = $request->file('foto')->store('membros/fotos', 'public');
        }

        $membro->update($validated);

        return redirect()->route('admin.membros.index')
            ->with('success', 'Membro atualizado com sucesso.');
    }

    public function destroy(Membro $membro): RedirectResponse
    {
        $membro->delete();

        return redirect()->route('admin.membros.index')
            ->with('success', 'Membro excluído com sucesso.');
    }
}
