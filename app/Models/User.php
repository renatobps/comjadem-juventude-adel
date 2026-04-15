<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return HasOne<Membro, $this>
     */
    public function membroPorEmail(): HasOne
    {
        return $this->hasOne(Membro::class, 'email', 'email');
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function regionalScopeId(): ?int
    {
        return $this->regionalScopeIds()[0] ?? null;
    }

    /**
     * @return array<int, int>
     */
    public function regionalScopeIds(): array
    {
        if ($this->isAdmin()) {
            return [];
        }

        $membro = $this->membroPorEmail()->with('acessosRegionais')->first();

        if (! $membro) {
            return [];
        }

        return $membro->acessosRegionais
            ->pluck('regional_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    public function isRegionalLeader(): bool
    {
        return ! $this->isAdmin() && count($this->regionalScopeIds()) > 0;
    }

    public function canAccessAdminPanel(): bool
    {
        return $this->isAdmin() || $this->isRegionalLeader();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }
}
