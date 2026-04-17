<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class NotificacaoPosInscricaoConfig
{
    public static function mensagemPosInscricao(): string
    {
        $data = self::ler();
        $mensagem = (string) ($data['mensagem_pos_inscricao'] ?? $data['mensagem'] ?? '');

        return $mensagem !== '' ? $mensagem : self::mensagemPosInscricaoPadrao();
    }

    public static function salvarMensagemPosInscricao(string $mensagem): void
    {
        $payload = self::ler();
        $payload['mensagem_pos_inscricao'] = trim($mensagem);
        self::escrever($payload);
    }

    public static function mensagemConfirmada(): string
    {
        $data = self::ler();
        $mensagem = (string) ($data['mensagem_confirmada'] ?? '');

        return $mensagem !== '' ? $mensagem : self::mensagemConfirmadaPadrao();
    }

    public static function salvarMensagemConfirmada(string $mensagem): void
    {
        $payload = self::ler();
        $payload['mensagem_confirmada'] = trim($mensagem);
        self::escrever($payload);
    }

    public static function mensagemPosInscricaoPadrao(): string
    {
        return "A paz do Senhor {nome_do_inscrito}, ! 🙌\n"
            . "Sua pré-inscrição foi realizada com sucesso!\n\n"
            . "Prepare-se para viver um momento inesquecível na presença de Deus, com o tema Unidade, Hierarquia e Disciplina — será um tempo de alinhamento, crescimento e avivamento espiritual!\n\n"
            . "Para concluir sua inscrição, procure o seu líder de jovens e realize o pagamento do ingresso com camiseta.\n"
            . "Após a confirmação de todos os dados, você receberá a validação final da sua inscrição.\n\n"
            . "📌 Tamanho da camiseta: {tamanho_camiseta}\n"
            . "📌 Valor da inscrição:\n\n"
            . "Qualquer dúvida, estamos à disposição!\n"
            . "Nos vemos lá! 🔥";
    }

    public static function mensagemConfirmadaPadrao(): string
    {
        return "*A paz do Senhor {nome_do_inscrito}! 🙌*\n"
            . "_Seu pagamento foi confirmado com sucesso!_\n\n"
            . "> Sua inscrição no COMJADEM está oficialmente validada! Prepare-se para viver dias marcantes na presença de Deus, com o tema *Unidade, Hierarquia e Disciplina*.\n\n"
            . "Agora é só se preparar espiritualmente e alinhar todos os detalhes para esse grande evento!\n\n"
            . "📌 *Status da inscrição:* Confirmada\n"
            . "📌 *Camiseta:* {tamanho_camiseta}\n\n"
            . "Qualquer dúvida, estamos à disposição!\n"
            . "*Nos vemos lá!* 🔥";
    }

    /**
     * Backward compatibility.
     */
    public static function mensagem(): string
    {
        return self::mensagemPosInscricao();
    }

    /**
     * Backward compatibility.
     */
    public static function salvarMensagem(string $mensagem): void
    {
        self::salvarMensagemPosInscricao($mensagem);
    }

    /**
     * Backward compatibility.
     */
    public static function mensagemPadrao(): string
    {
        return self::mensagemPosInscricaoPadrao();
    }

    /**
     * @return array<string, mixed>
     */
    private static function ler(): array
    {
        $path = self::path();
        if (! File::exists($path)) {
            return [];
        }

        $decoded = json_decode((string) File::get($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private static function escrever(array $payload): void
    {
        File::put(self::path(), json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private static function path(): string
    {
        return storage_path('app/notificacao-pos-inscricao.json');
    }
}
