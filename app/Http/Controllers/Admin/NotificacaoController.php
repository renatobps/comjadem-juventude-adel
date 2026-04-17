<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PreInscricao;
use App\Support\NotificacaoHistorico;
use App\Support\NotificacaoPosInscricaoConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class NotificacaoController extends Controller
{
    public function index(Request $request): View
    {
        $statusHistorico = (string) $request->query('status_historico', 'todos');
        $dataInicioHistorico = (string) $request->query('data_inicio', '');
        $dataFimHistorico = (string) $request->query('data_fim', '');

        return view('admin.notificacoes.index', [
            'historicoNotificacoes' => NotificacaoHistorico::listar([
                'status' => $statusHistorico,
                'data_inicio' => $dataInicioHistorico,
                'data_fim' => $dataFimHistorico,
            ]),
            'statusHistorico' => $statusHistorico,
            'dataInicioHistorico' => $dataInicioHistorico,
            'dataFimHistorico' => $dataFimHistorico,
            'mensagemPosInscricao' => NotificacaoPosInscricaoConfig::mensagemPosInscricao(),
            'mensagemPosInscricaoPadrao' => NotificacaoPosInscricaoConfig::mensagemPosInscricaoPadrao(),
            'mensagemConfirmada' => NotificacaoPosInscricaoConfig::mensagemConfirmada(),
            'mensagemConfirmadaPadrao' => NotificacaoPosInscricaoConfig::mensagemConfirmadaPadrao(),
        ]);
    }

    public function salvarMensagemPosInscricao(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mensagem_pos_inscricao' => ['required', 'string', 'max:8000'],
        ]);

        NotificacaoPosInscricaoConfig::salvarMensagemPosInscricao($validated['mensagem_pos_inscricao']);

        return back()->with('success', 'Mensagem pós-inscrição salva com sucesso.');
    }

    public function salvarMensagemConfirmada(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mensagem_confirmada' => ['required', 'string', 'max:8000'],
        ]);

        NotificacaoPosInscricaoConfig::salvarMensagemConfirmada($validated['mensagem_confirmada']);

        return back()->with('success', 'Mensagem de inscrição confirmada salva com sucesso.');
    }

    public function configuracaoWpp(Request $request): View
    {
        $action = (string) $request->query('action', 'all');
        $baseUrl = (string) config('services.evolution_go.base_url');
        $instance = (string) config('services.evolution_go.instance_name');
        $instanceToken = (string) config('services.evolution_go.instance_token');
        $apiKey = (string) config('services.evolution_go.api_key');

        $status = null;
        $qrCode = null;
        $instanceInfo = null;
        $erros = [];

        if ($baseUrl === '' || $instance === '' || ($apiKey === '' && $instanceToken === '')) {
            $erros[] = 'Configure EVOLUTION_GO_BASE_URL, EVOLUTION_GO_INSTANCE_NAME e EVOLUTION_GO_INSTANCE_TOKEN (ou EVOLUTION_GO_API_KEY) no .env.';
        } else {
            if (in_array($action, ['status', 'all'], true)) {
                $statusResponse = $this->evolutionGet($baseUrl, '/instance/status', $apiKey, $instanceToken);
                if ($statusResponse !== null && $statusResponse->ok()) {
                    $status = data_get($statusResponse->json(), 'data');
                    if ($action === 'status') {
                        $this->registrarAtividadeWpp('status', 'Status verificado', 'ok');
                    }
                } else {
                    $erros[] = 'Não foi possível obter o status da instância.';
                    if ($action === 'status') {
                        $this->registrarAtividadeWpp('status', 'Erro ao verificar status', 'erro');
                    }
                }
            }

            if (in_array($action, ['qr', 'all'], true)) {
                $qrResponse = $this->evolutionGet($baseUrl, '/instance/qr', $apiKey, $instanceToken);
                if ($qrResponse !== null && $qrResponse->ok()) {
                    $qrCode = (string) data_get($qrResponse->json(), 'data.Qrcode', '');
                    if ($action === 'qr') {
                        $this->registrarAtividadeWpp('qrcode', 'QR Code obtido', 'ok');
                    }
                } else {
                    $erros[] = 'Não foi possível obter o QR Code da instância.';
                    if ($action === 'qr') {
                        $this->registrarAtividadeWpp('qrcode', 'Erro ao gerar QR Code', 'erro');
                    }
                }
            }

            $allResponse = $this->evolutionGet($baseUrl, '/instance/all', $apiKey, $instanceToken);
            if ($allResponse !== null && $allResponse->ok()) {
                $instancias = collect(data_get($allResponse->json(), 'data', []));
                $instanceInfo = $instancias->first(function (mixed $item) use ($instance): bool {
                    return (string) data_get($item, 'name') === $instance;
                });
            } else {
                $erros[] = 'Não foi possível listar as instâncias.';
            }
        }

        return view('admin.notificacoes.configuracao-wpp', [
            'instanceName' => $instance,
            'status' => $status,
            'qrCode' => $qrCode,
            'instanceInfo' => $instanceInfo,
            'erros' => $erros,
            'action' => $action,
            'departamentos' => [
                'jovens' => 'Jovens',
                'secretaria' => 'Secretaria',
                'louvor' => 'Louvor',
                'midia' => 'Mídia',
            ],
        ]);
    }

    public function enviarTesteNumeroWpp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'numero_teste' => ['required', 'string', 'max:20'],
            'mensagem_teste' => ['required', 'string', 'max:4096'],
        ]);

        $numero = $this->normalizeBrazilWhatsAppNumber($validated['numero_teste']);
        if ($numero === null) {
            return back()->with('error', 'Número inválido para teste. Use DDD + número (ex.: 61993640457).');
        }

        $ok = $this->enviarTextoViaEvolution($numero, trim($validated['mensagem_teste']));
        $mensagemTeste = trim($validated['mensagem_teste']);

        if (! $ok) {
            $this->registrarAtividadeWpp('teste', 'Falha no envio de mensagem de teste', 'erro');
            NotificacaoHistorico::registrar($numero, $mensagemTeste, 'erro');
            return back()->with('error', 'Falha ao enviar mensagem de teste.');
        }

        $this->registrarAtividadeWpp('teste', 'Mensagem de teste enviada', 'ok');
        NotificacaoHistorico::registrar($numero, $mensagemTeste, 'enviada');
        return back()->with('success', 'Mensagem de teste enviada com sucesso.');
    }

    public function enviarTesteDepartamentoWpp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'departamento' => ['required', 'string', 'in:jovens,secretaria,louvor,midia'],
            'mensagem_departamento' => ['required', 'string', 'max:4096'],
        ]);

        $numeroDepartamentoRaw = (string) env('EVOLUTION_GO_TEST_NUMBER', '');
        $numeroDepartamento = $this->normalizeBrazilWhatsAppNumber($numeroDepartamentoRaw);
        if ($numeroDepartamento === null) {
            return back()->with(
                'error',
                'Configure EVOLUTION_GO_TEST_NUMBER no .env para habilitar teste por departamento.'
            );
        }

        $labelDepartamento = match ($validated['departamento']) {
            'jovens' => 'Jovens',
            'secretaria' => 'Secretaria',
            'louvor' => 'Louvor',
            default => 'Mídia',
        };

        $mensagem = '[Departamento: ' . $labelDepartamento . '] ' . trim($validated['mensagem_departamento']);
        $ok = $this->enviarTextoViaEvolution($numeroDepartamento, $mensagem);

        if (! $ok) {
            $this->registrarAtividadeWpp('teste', 'Falha no envio de teste para departamento', 'erro');
            NotificacaoHistorico::registrar($numeroDepartamento, $mensagem, 'erro');
            return back()->with('error', 'Falha ao enviar teste para departamento.');
        }

        $this->registrarAtividadeWpp('teste', 'Mensagem de teste para departamento enviada', 'ok');
        NotificacaoHistorico::registrar($numeroDepartamento, $mensagem, 'enviada');
        return back()->with('success', 'Mensagem de teste para departamento enviada com sucesso.');
    }

    public function enviarTexto(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'numero' => ['required', 'string', 'max:20'],
            'mensagem' => ['required', 'string', 'max:4096'],
            'arquivo' => ['nullable', 'file', 'max:51200'],
        ]);

        $baseUrl = (string) config('services.evolution_go.base_url');
        $instance = (string) config('services.evolution_go.instance_name');
        $instanceToken = (string) config('services.evolution_go.instance_token');
        $apiKey = (string) config('services.evolution_go.api_key');
        $endpointTemplate = (string) config('services.evolution_go.text_endpoint');

        if ($baseUrl === '' || $instance === '' || ($apiKey === '' && $instanceToken === '')) {
            return back()
                ->withInput()
                ->with('error', 'Configure EVOLUTION_GO_BASE_URL, EVOLUTION_GO_INSTANCE_NAME e EVOLUTION_GO_INSTANCE_TOKEN (ou EVOLUTION_GO_API_KEY) no .env.');
        }

        $number = $this->normalizeBrazilWhatsAppNumber($validated['numero']);
        if ($number === null) {
            return back()
                ->withInput()
                ->with('error', 'Número inválido. Digite no formato 61993640457 (DDD + número, sem o 55).');
        }

        $isMedia = $request->hasFile('arquivo');
        $mediaMeta = null;
        if ($isMedia) {
            $uploadedFile = $request->file('arquivo');
            $storedPath = $uploadedFile->store('notificacoes/midias', 'public');
            $publicPath = Storage::disk('public')->url($storedPath);
            $mediaUrl = str_starts_with($publicPath, 'http') ? $publicPath : url($publicPath);
            $originalName = $uploadedFile->getClientOriginalName();
            $filename = pathinfo($originalName, PATHINFO_FILENAME) ?: 'arquivo';
            $mediaType = $this->detectMediaType($uploadedFile->getMimeType(), $originalName);

            $mediaMeta = [
                'url' => $mediaUrl,
                'type' => $mediaType,
                'filename' => $filename,
            ];
        }

        $message = trim($validated['mensagem']);
        $endpoints = $this->endpointsForMode($endpointTemplate, $isMedia);

        $authVariants = $this->authVariants($apiKey, $instanceToken);

        $lastError = 'Falha ao enviar notificação para o WhatsApp.';
        foreach ($endpoints as $endpoint) {
            $resolvedEndpoint = str_replace('{instance}', $instance, $endpoint);
            $url = rtrim($baseUrl, '/') . '/' . ltrim($resolvedEndpoint, '/');
            $payload = $this->buildPayloadForEndpoint($resolvedEndpoint, $instance, $number, $message, $mediaMeta);

            foreach ($authVariants as $variant) {
                $requestUrl = $url;
                if (! empty($variant['query'])) {
                    $separator = str_contains($requestUrl, '?') ? '&' : '?';
                    $requestUrl .= $separator . http_build_query($variant['query']);
                }

                $response = Http::withHeaders($variant['headers'])
                    ->timeout(20)
                    ->post($requestUrl, $payload);

                if ($response->status() === 404) {
                    // endpoint inexistente, tenta o próximo endpoint
                    break;
                }

                if ($response->failed()) {
                    $errorMessage = (string) data_get($response->json(), 'error.message', $response->body());
                    $lastError = $errorMessage !== '' ? $errorMessage : $lastError;
                    // tenta próximo formato de autenticação para o mesmo endpoint
                    continue;
                }

                NotificacaoHistorico::registrar($number, $message, 'enviada');
                return back()->with('success', 'Mensagem enviada com sucesso para o WhatsApp');
            }
        }

        NotificacaoHistorico::registrar($number, $message, 'erro');
        return back()
            ->withInput()
            ->with('error', 'Evolution Go retornou erro: ' . $lastError);
    }

    public function enviarParaInscricoes(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mensagem' => ['required', 'string', 'max:4096'],
            'destinatario_tipo' => ['required', 'string', 'in:status,igreja,regional'],
            'status_destinatario' => ['nullable', 'string', 'in:aguardando,confirmada,cancelada'],
            'igreja_id' => ['nullable', 'integer', 'exists:igrejas,id'],
            'regional_id' => ['nullable', 'integer', 'exists:regionais,id'],
        ]);

        $query = PreInscricao::query();
        $tipo = $validated['destinatario_tipo'];

        if ($tipo === 'status') {
            $statusDestinatario = $validated['status_destinatario'] ?? PreInscricao::STATUS_AGUARDANDO;
            if ($statusDestinatario === PreInscricao::STATUS_AGUARDANDO) {
                $query->where(function ($q): void {
                    $q->where('status', PreInscricao::STATUS_AGUARDANDO)
                        ->orWhereNull('status');
                });
            } else {
                $query->where('status', $statusDestinatario);
            }
        }

        if ($tipo === 'igreja') {
            if (empty($validated['igreja_id'])) {
                return back()->withInput()->with('error', 'Selecione uma igreja para enviar.');
            }
            $query->where('igreja_id', (int) $validated['igreja_id']);
        }

        if ($tipo === 'regional') {
            if (empty($validated['regional_id'])) {
                return back()->withInput()->with('error', 'Selecione uma regional para enviar.');
            }
            $regionalId = (int) $validated['regional_id'];
            $query->whereHas('igrejaRel', fn ($q) => $q->where('regional_id', $regionalId));
        }

        $inscricoes = $query->get(['whatsapp']);
        $numeros = $inscricoes
            ->pluck('whatsapp')
            ->map(fn ($item) => $this->normalizeBrazilWhatsAppNumber((string) $item))
            ->filter()
            ->unique()
            ->values();

        if ($numeros->isEmpty()) {
            return back()->withInput()->with('error', 'Nenhum destinatário válido encontrado para esse filtro.');
        }

        $mensagem = trim($validated['mensagem']);
        $ok = 0;
        $erro = 0;

        foreach ($numeros as $numero) {
            if ($this->enviarTextoViaEvolution((string) $numero, $mensagem)) {
                $ok++;
            } else {
                $erro++;
            }
        }

        if ($ok === 0) {
            return back()->withInput()->with('error', 'Não foi possível enviar a notificação para os destinatários selecionados.');
        }

        $msg = "Envio concluído. Sucesso: {$ok}.";
        if ($erro > 0) {
            $msg .= " Falhas: {$erro}.";
        }

        return back()->with('success', $msg);
    }

    /**
     * @return array<int, string>
     */
    private function endpointsForMode(string $configuredTextEndpoint, bool $isMedia): array
    {
        if ($isMedia) {
            return [
                '/send/media',
            ];
        }

        $normalizedConfigured = '/' . ltrim(trim($configuredTextEndpoint), '/');

        return array_values(array_unique([
            $normalizedConfigured,
            '/send/text',
            '/message/sendText/{instance}',
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayloadForEndpoint(string $endpoint, string $instance, string $number, string $message, ?array $mediaMeta = null): array
    {
        if ($mediaMeta !== null && str_contains($endpoint, '/send/media')) {
            return [
                'caption' => $message,
                'delay' => 500,
                'filename' => $mediaMeta['filename'],
                'number' => $number,
                'type' => $mediaMeta['type'],
                'url' => $mediaMeta['url'],
            ];
        }

        if (str_contains($endpoint, '/send/text')) {
            return [
                'number' => $number,
                'text' => $message,
                'delay' => 500,
            ];
        }

        return [
            'number' => $number,
            'text' => $message,
            'delay' => 1200,
        ];
    }

    /**
     * @return array<int, array{headers: array<string, string>, query: array<string, string>}>
     */
    private function authVariants(string $apiKey, string $instanceToken): array
    {
        $baseHeaders = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $primaryApiKey = $instanceToken !== '' ? $instanceToken : $apiKey;

        $variants = [
            [
                'headers' => $baseHeaders + ['apikey' => $primaryApiKey],
                'query' => [],
            ],
        ];

        if ($apiKey !== '' && $apiKey !== $primaryApiKey) {
            $variants[] = [
                'headers' => $baseHeaders + ['apikey' => $apiKey],
                'query' => [],
            ];
        }

        if ($instanceToken !== '') {
            $variants[] = [
                'headers' => $baseHeaders + ['apikey' => $primaryApiKey, 'Authorization' => 'Bearer ' . $instanceToken],
                'query' => [],
            ];
            $variants[] = [
                'headers' => $baseHeaders + ['apikey' => $primaryApiKey, 'token' => $instanceToken],
                'query' => [],
            ];
            $variants[] = [
                'headers' => $baseHeaders + ['apikey' => $primaryApiKey, 'instance-token' => $instanceToken],
                'query' => [],
            ];
            $variants[] = [
                'headers' => $baseHeaders + ['apikey' => $primaryApiKey],
                'query' => ['token' => $instanceToken],
            ];
        }

        return $variants;
    }

    private function normalizeBrazilWhatsAppNumber(string $rawNumber): ?string
    {
        $digits = preg_replace('/\D+/', '', $rawNumber) ?: '';

        // Usuário digita somente DDD + número (11 dígitos), sistema adiciona o 55.
        if (strlen($digits) === 11) {
            return '55' . $digits;
        }

        // Aceita também se vier completo com país.
        if (strlen($digits) === 13 && str_starts_with($digits, '55')) {
            return $digits;
        }

        return null;
    }

    private function detectMediaType(?string $mimeType, string $originalName): string
    {
        $mime = strtolower((string) $mimeType);
        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }
        if (str_starts_with($mime, 'audio/')) {
            return 'audio';
        }

        $ext = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'], true)) {
            return 'image';
        }
        if (in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'], true)) {
            return 'video';
        }
        if (in_array($ext, ['mp3', 'ogg', 'wav', 'aac', 'm4a'], true)) {
            return 'audio';
        }

        return 'document';
    }

    private function evolutionGet(string $baseUrl, string $endpoint, string $apiKey, string $instanceToken): ?Response
    {
        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
        $authVariants = $this->authVariants($apiKey, $instanceToken);

        foreach ($authVariants as $variant) {
            $requestUrl = $url;
            if (! empty($variant['query'])) {
                $separator = str_contains($requestUrl, '?') ? '&' : '?';
                $requestUrl .= $separator . http_build_query($variant['query']);
            }

            $response = Http::withHeaders($variant['headers'])
                ->timeout(20)
                ->get($requestUrl);

            if ($response->status() === 404 || $response->status() === 401 || $response->status() === 403) {
                continue;
            }

            return $response;
        }

        return null;
    }

    private function enviarTextoViaEvolution(string $number, string $message): bool
    {
        $baseUrl = (string) config('services.evolution_go.base_url');
        $instance = (string) config('services.evolution_go.instance_name');
        $instanceToken = (string) config('services.evolution_go.instance_token');
        $apiKey = (string) config('services.evolution_go.api_key');
        $endpointTemplate = (string) config('services.evolution_go.text_endpoint');

        if ($baseUrl === '' || $instance === '' || ($apiKey === '' && $instanceToken === '')) {
            return false;
        }

        $endpoints = $this->endpointsForMode($endpointTemplate, false);
        $authVariants = $this->authVariants($apiKey, $instanceToken);

        foreach ($endpoints as $endpoint) {
            $resolvedEndpoint = str_replace('{instance}', $instance, $endpoint);
            $url = rtrim($baseUrl, '/') . '/' . ltrim($resolvedEndpoint, '/');
            $payload = $this->buildPayloadForEndpoint($resolvedEndpoint, $instance, $number, $message);

            foreach ($authVariants as $variant) {
                $requestUrl = $url;
                if (! empty($variant['query'])) {
                    $separator = str_contains($requestUrl, '?') ? '&' : '?';
                    $requestUrl .= $separator . http_build_query($variant['query']);
                }

                $response = Http::withHeaders($variant['headers'])
                    ->timeout(20)
                    ->post($requestUrl, $payload);

                if ($response->status() === 404) {
                    break;
                }

                if ($response->failed()) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, array{hora: string, tipo: string, mensagem: string, status: string}>
     */
    private function carregarAtividadesWpp(int $limit = 12): array
    {
        $path = storage_path('app/wpp-atividades.json');
        if (! File::exists($path)) {
            return [];
        }

        $decoded = json_decode((string) File::get($path), true);
        if (! is_array($decoded)) {
            return [];
        }

        $itens = array_values(array_filter($decoded, fn ($item) => is_array($item)));

        return array_slice(array_reverse($itens), 0, $limit);
    }

    private function registrarAtividadeWpp(string $tipo, string $mensagem, string $status): void
    {
        $path = storage_path('app/wpp-atividades.json');
        $existente = [];

        if (File::exists($path)) {
            $decoded = json_decode((string) File::get($path), true);
            if (is_array($decoded)) {
                $existente = $decoded;
            }
        }

        $existente[] = [
            'hora' => now()->format('H:i:s'),
            'tipo' => $tipo,
            'mensagem' => $mensagem,
            'status' => $status,
        ];

        $existente = array_slice($existente, -200);
        File::put($path, json_encode($existente, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
