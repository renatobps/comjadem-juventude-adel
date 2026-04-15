<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nome'])]
class Cargo extends Model
{
    protected $table = 'cargos';

    /**
     * @return HasMany<Membro, $this>
     */
    public function membros(): HasMany
    {
        return $this->hasMany(Membro::class);
    }
}
