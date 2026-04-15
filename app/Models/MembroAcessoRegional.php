<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['membro_id', 'regional_id'])]
class MembroAcessoRegional extends Model
{
    protected $table = 'membro_acesso_regionais';

    /**
     * @return BelongsTo<Membro, $this>
     */
    public function membro(): BelongsTo
    {
        return $this->belongsTo(Membro::class);
    }

    /**
     * @return BelongsTo<Regional, $this>
     */
    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class);
    }
}
