<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreInscricao;
use App\Support\NotificacaoHistorico;
use App\Support\NotificacaoPosInscricaoConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        $statusAnterior = $pre_inscricao->status;
        $pre_inscricao->update(['status' => $validated['status']]);

        if ($statusAnterior !== PreInscricao::STATUS_CONFIRMADA && $pre_inscricao->status === PreInscricao::STATUS_CONFIRMADA) {
            $this->enviarNotificacaoConfirmada($pre_inscricao);
        }

        return response()->json([
            'ok' => true,
            'status' => $pre_inscricao->status,
        ]);
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
                Log::warning('Falha ao enviar notificação de confirmação.', [
                    'pre_inscricao_id' => $inscricao->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return;
            }

            NotificacaoHistorico::registrar($numero, $mensagem, 'enviada');
        } catch (\Throwable $e) {
            NotificacaoHistorico::registrar($numero, $mensagem, 'erro');
            Log::warning('Exceção ao enviar notificação de confirmação.', [
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
