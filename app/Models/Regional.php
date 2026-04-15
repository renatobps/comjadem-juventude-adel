<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nome', 'pastor_responsavel'])]
class Regional extends Model
{
    /** Tabela em português (o inflector inglês usaria "regionals"). */
    protected $table = 'regionais';

    /**
     * @return HasMany<Igreja, $this>
     */
    public function igrejas(): HasMany
    {
        return $this->hasMany(Igreja::class);
    }

    public function label(): string
    {
        return $this->nome.' — '.$this->pastor_responsavel;
    }
}
