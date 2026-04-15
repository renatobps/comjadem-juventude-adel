<?php

namespace Database\Seeders;

use App\Models\Igreja;
use App\Models\PreInscricao;
use App\Models\Regional;
use Illuminate\Database\Seeder;

class PreInscricoesFakeSeeder extends Seeder
{
    public function run(): void
    {
        $igrejas = Igreja::query()
            ->with('regional')
            ->get()
            ->filter(fn (Igreja $igreja) => $igreja->regional !== null)
            ->values();

        if ($igrejas->isEmpty()) {
            $this->command?->warn('Nenhuma igreja com regional encontrada. Cadastre igrejas antes de popular pré-inscrições.');
            return;
        }

        $regionaisComIgreja = $igrejas
            ->pluck('regional_id')
            ->filter()
            ->unique()
            ->values();

        $todasRegionais = Regional::query()->pluck('id');
        $regionaisSemIgreja = $todasRegionais->diff($regionaisComIgreja);

        if ($regionaisSemIgreja->isNotEmpty()) {
            $this->command?->warn('Algumas regionais não têm igrejas vinculadas e não terão inscrições fake.');
        }

        PreInscricao::query()->delete();

        $statuses = [
            PreInscricao::STATUS_AGUARDANDO,
            PreInscricao::STATUS_CONFIRMADA,
            PreInscricao::STATUS_CANCELADA,
        ];

        $criarInscricao = function (Igreja $igreja) use ($statuses): void {
            $digits = fake()->numerify('###########');

            PreInscricao::query()->create([
                'nome' => fake()->name(),
                'idade' => fake()->numberBetween(12, 45),
                'whatsapp' => sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 5), substr($digits, 7, 4)),
                'igreja_id' => $igreja->id,
                'igreja' => $igreja->nomeNoFormulario(),
                'lider_jovens' => fake()->boolean(30),
                'status' => fake()->randomElement($statuses),
                'created_at' => fake()->dateTimeBetween('-90 days', 'now'),
                'updated_at' => now(),
            ]);
        };

        foreach ($regionaisComIgreja as $regionalId) {
            $igrejaDaRegional = $igrejas->firstWhere('regional_id', $regionalId);
            if ($igrejaDaRegional) {
                $criarInscricao($igrejaDaRegional);
            }
        }

        $faltantes = max(0, 100 - PreInscricao::query()->count());
        for ($i = 0; $i < $faltantes; $i++) {
            $criarInscricao($igrejas->random());
        }

        $this->command?->info('Pré-inscrições fake geradas com sucesso: '.PreInscricao::query()->count());
    }
}
