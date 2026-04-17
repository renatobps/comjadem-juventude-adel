<?php

namespace App\Http\Controllers;

use App\Models\Igreja;
use App\Models\PreInscricao;
use App\Support\NotificacaoHistorico;
use App\Support\NotificacaoPosInscricaoConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PreInscricaoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'idade' => ['required', 'integer', 'min:10', 'max:120'],
            'whatsapp' => ['required', 'string', 'max:40'],
            'tamanho_camiseta' => ['required', 'string', 'in:P,M,G,GG,XG'],
            'igreja_id' => ['required', 'integer', 'exists:igrejas,id'],
            'lider' => ['required', 'in:sim,nao'],
        ]);

        $igreja = Igreja::query()->with('regional')->findOrFail($validated['igreja_id']);

        $inscricao = PreInscricao::query()->create([
            'nome' => $validated['nome'],
            'idade' => $validated['idade'],
            'whatsapp' => $validated['whatsapp'],
            'tamanho_camiseta' => $validated['tamanho_camiseta'],
            'igreja_id' => $igreja->id,
            'igreja' => $igreja->nomeNoFormulario(),
            'lider_jovens' => $validated['lider'] === 'sim',
        ]);

        $this->enviarNotificacaoPreInscricao($inscricao);

        return response()->json([
            'message' => 'Pré-inscrição registrada com sucesso.',
        ], 201);
    }

    private function enviarNotificacaoPreInscricao(PreInscricao $inscricao): void
    {
        $baseUrl = (string) config('services.evolution_go.base_url');
        $instanceToken = (string) config('services.evolution_go.instance_token');
        $apiKey = (string) config('services.evolution_go.api_key');
        $endpoint = '/send/media';

        if ($baseUrl === '' || ($instanceToken === '' && $apiKey === '')) {
            return;
        }

        $numero = $this->normalizarNumeroWhatsapp($inscricao->whatsapp);
        if ($numero === null) {
            return;
        }

        $mensagemTemplate = NotificacaoPosInscricaoConfig::mensagemPosInscricao();
        $mensagem = strtr($mensagemTemplate, [
            '{nome_do_inscrito}' => $inscricao->nome,
            '{tamanho_camiseta}' => (string) $inscricao->tamanho_camiseta,
        ]);
        $payload = [
            'caption' => $mensagem,
            'delay' => 500,
            'filename' => 'comj',
            'number' => $numero,
            'type' => 'image',
            'url' => 'https://freeimage.host/i/comj.BUL1MDG',
        ];
        $payloadTexto = [
            'number' => $numero,
            'text' => $mensagem,
            'delay' => 500,
        ];

        $apiKeyHeader = $instanceToken !== '' ? $instanceToken : $apiKey;

        try {
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'apikey' => $apiKeyHeader,
            ];

            $response = Http::withHeaders($headers)
                ->timeout(20)
                ->post(rtrim($baseUrl, '/') . $endpoint, $payload);

            if ($response->failed()) {
                Log::warning('Falha no envio de mídia da pré-inscrição. Tentando fallback para texto.', [
                    'pre_inscricao_id' => $inscricao->id,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                $responseTexto = Http::withHeaders($headers)
                    ->timeout(20)
                    ->post(rtrim($baseUrl, '/') . '/send/text', $payloadTexto);

                if ($responseTexto->failed()) {
                    NotificacaoHistorico::registrar($numero, $mensagem, 'erro');
                    Log::warning('Falha também no fallback de texto da pré-inscrição.', [
                        'pre_inscricao_id' => $inscricao->id,
                        'status' => $responseTexto->status(),
                        'response' => $responseTexto->body(),
                    ]);
                } else {
                    NotificacaoHistorico::registrar($numero, $mensagem, 'enviada');
                }
            } else {
                NotificacaoHistorico::registrar($numero, $mensagem, 'enviada');
            }
        } catch (\Throwable $e) {
            NotificacaoHistorico::registrar($numero, $mensagem, 'erro');
            Log::warning('Falha ao enviar notificação de pré-inscrição para WhatsApp.', [
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
