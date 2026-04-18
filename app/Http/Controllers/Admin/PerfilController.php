<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PerfilController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        abort_unless($user, 403, 'Usuário não autenticado.');

        $membro = $this->obterOuCriarMembro($user);

        return view('admin.perfil.edit', [
            'membro' => $membro,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user, 403, 'Usuário não autenticado.');

        $membro = $this->obterOuCriarMembro($user);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:membros,email,' . $membro->id, 'unique:users,email,' . $user->id],
            'telefone' => ['required', 'string', 'max:40'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'senha' => ['nullable', 'string', 'min:6', 'max:255', 'confirmed'],
        ]);

        if (! array_key_exists('senha', $validated) || $validated['senha'] === null) {
            unset($validated['senha']);
        }

        if ($request->hasFile('foto')) {
            if ($membro->foto) {
                Storage::disk('public')->delete($membro->foto);
            }
            $validated['foto'] = $request->file('foto')->store('membros/fotos', 'public');
        }

        DB::transaction(function () use ($membro, $user, $validated): void {
            $membro->update($validated);

            $userData = [
                'name' => $validated['nome'],
                'email' => $validated['email'],
            ];

            if (array_key_exists('senha', $validated)) {
                $userData['password'] = $validated['senha'];
            }

            $user->update($userData);
        });

        return redirect()
            ->route('admin.perfil.edit')
            ->with('success', 'Perfil atualizado com sucesso.');
    }

    private function obterOuCriarMembro($user): Membro
    {
        return Membro::query()->firstOrCreate(
            ['email' => $user->email],
            [
                'nome' => $user->name,
                'email' => $user->email,
                'senha' => $user->password,
                'telefone' => '',
            ]
        );
    }
}
