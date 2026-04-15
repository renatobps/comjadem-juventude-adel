<?php

namespace App\Http\Controllers;

use App\Models\Igreja;
use App\Models\PreInscricao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreInscricaoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'idade' => ['required', 'integer', 'min:10', 'max:120'],
            'whatsapp' => ['required', 'string', 'max:40'],
            'igreja_id' => ['required', 'integer', 'exists:igrejas,id'],
            'lider' => ['required', 'in:sim,nao'],
        ]);

        $igreja = Igreja::query()->with('regional')->findOrFail($validated['igreja_id']);

        PreInscricao::query()->create([
            'nome' => $validated['nome'],
            'idade' => $validated['idade'],
            'whatsapp' => $validated['whatsapp'],
            'igreja_id' => $igreja->id,
            'igreja' => $igreja->nomeNoFormulario(),
            'lider_jovens' => $validated['lider'] === 'sim',
        ]);

        return response()->json([
            'message' => 'Pré-inscrição registrada com sucesso.',
        ], 201);
    }
}
