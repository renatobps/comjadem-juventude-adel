<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Membro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembroPublicoController extends Controller
{
    public function create(): View
    {
        return view('membros.publico', [
            'cargos' => Cargo::query()->orderBy('nome')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:membros,email'],
            'senha' => ['required', 'string', 'min:6', 'max:255'],
            'cargo_id' => ['required', 'exists:cargos,id'],
            'telefone' => ['required', 'string', 'max:40'],
        ]);

        Membro::query()->create($validated);

        return redirect()
            ->route('membros.publico.create')
            ->with('success', 'Cadastro enviado com sucesso.');
    }
}
