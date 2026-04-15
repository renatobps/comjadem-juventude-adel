<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['bairro', 'dirigente', 'dirigente_membro_id', 'regional_id'])]
class Igreja extends Model
{
    protected $table = 'igrejas';

    /**
     * @return BelongsTo<Regional, $this>
     */
    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class);
    }

    /**
     * @return BelongsTo<Membro, $this>
     */
    public function dirigenteMembro(): BelongsTo
    {
        return $this->belongsTo(Membro::class, 'dirigente_membro_id');
    }

    /** Nome exibido no formulário público. */
    public function nomeNoFormulario(): string
    {
        return $this->bairro;
    }
}
