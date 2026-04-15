<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreInscricao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreInscricaoStatusController extends Controller
{
    public function update(Request $request, PreInscricao $pre_inscricao): JsonResponse
    {
        $regionalScopeIds = $request->user()?->regionalScopeIds() ?? [];
        if (! empty($regionalScopeIds)) {
            $allowed = PreInscricao::query()
                ->whereKey($pre_inscricao->id)
                ->whereHas('igrejaRel', fn ($q) => $q->whereIn('regional_id', $regionalScopeIds))
                ->exists();

            if (! $allowed) {
                abort(403, 'Você não pode alterar inscrições de outra regional.');
            }
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:aguardando,confirmada,cancelada'],
        ]);

        $pre_inscricao->update(['status' => $validated['status']]);

        return response()->json([
            'ok' => true,
            'status' => $pre_inscricao->status,
        ]);
    }
}
