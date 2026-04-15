<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['nome', 'idade', 'whatsapp', 'igreja', 'igreja_id', 'lider_jovens', 'status'])]
class PreInscricao extends Model
{
    public const STATUS_AGUARDANDO = 'aguardando';

    public const STATUS_CONFIRMADA = 'confirmada';

    public const STATUS_CANCELADA = 'cancelada';

    protected $table = 'pre_inscricoes';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'idade' => 'integer',
            'lider_jovens' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Igreja, $this>
     */
    public function igrejaRel(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_AGUARDANDO => 'Aguardando',
            self::STATUS_CONFIRMADA => 'Confirmada',
            self::STATUS_CANCELADA => 'Cancelada',
        ];
    }
}
