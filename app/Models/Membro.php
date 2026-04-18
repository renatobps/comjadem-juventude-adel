<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nome', 'email', 'senha', 'foto', 'cargo_id', 'telefone'])]
class Membro extends Model
{
    protected $table = 'membros';
    protected $hidden = ['senha'];

    protected function casts(): array
    {
        return [
            'senha' => 'hashed',
        ];
    }

    /**
     * @return BelongsTo<Cargo, $this>
     */
    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    /**
     * @return HasMany<MembroAcessoRegional, $this>
     */
    public function acessosRegionais(): HasMany
    {
        return $this->hasMany(MembroAcessoRegional::class);
    }

    public function setTelefoneAttribute($value): void
    {
        if ($value === null) {
            $this->attributes['telefone'] = null;

            return;
        }

        $this->attributes['telefone'] = preg_replace('/\D+/', '', (string) $value) ?? '';
    }
}
