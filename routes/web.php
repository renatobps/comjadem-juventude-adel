<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CargoController;
use App\Http\Controllers\Admin\ConfiguracaoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IgrejaController;
use App\Http\Controllers\Admin\MembroController;
use App\Http\Controllers\Admin\NotificacaoController;
use App\Http\Controllers\Admin\PreInscricaoController as AdminPreInscricaoController;
use App\Http\Controllers\Admin\PreInscricaoStatusController;
use App\Http\Controllers\Admin\RegionalController;
use App\Http\Controllers\MembroPublicoController;
use App\Models\Igreja;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing', [
        'igrejas' => Igreja::query()->with('regional')->orderBy('bairro')->get(),
    ]);
});

Route::get('/cadastro-membros', [MembroPublicoController::class, 'create'])->name('membros.publico.create');
Route::post('/cadastro-membros', [MembroPublicoController::class, 'store'])->name('membros.publico.store');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.perform');
    });

    Route::middleware(['auth', 'admin'])->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('igrejas', IgrejaController::class)->except(['show']);
        Route::get('inscricoes/{pre_inscricao}/edit', [AdminPreInscricaoController::class, 'edit'])
            ->name('inscricoes.edit');
        Route::put('inscricoes/{pre_inscricao}', [AdminPreInscricaoController::class, 'update'])
            ->name('inscricoes.update');
        Route::delete('inscricoes/{pre_inscricao}', [AdminPreInscricaoController::class, 'destroy'])
            ->name('inscricoes.destroy');
        Route::patch('inscricoes/{pre_inscricao}/status', [PreInscricaoStatusController::class, 'update'])
            ->name('inscricoes.status');
        Route::post('inscricoes/notificacoes/enviar', [NotificacaoController::class, 'enviarParaInscricoes'])
            ->name('inscricoes.notificacoes.enviar');

        Route::middleware('superadmin')->group(function (): void {
            Route::get('notificacoes', [NotificacaoController::class, 'index'])->name('notificacoes.index');
            Route::post('notificacoes/mensagem-pos-inscricao', [NotificacaoController::class, 'salvarMensagemPosInscricao'])
                ->name('notificacoes.mensagem-pos-inscricao');
            Route::post('notificacoes/mensagem-confirmada', [NotificacaoController::class, 'salvarMensagemConfirmada'])
                ->name('notificacoes.mensagem-confirmada');
            Route::get('notificacoes/configuracao-wpp', [NotificacaoController::class, 'configuracaoWpp'])->name('notificacoes.configuracao-wpp');
            Route::post('notificacoes/configuracao-wpp/teste-numero', [NotificacaoController::class, 'enviarTesteNumeroWpp'])
                ->name('notificacoes.configuracao-wpp.teste-numero');
            Route::post('notificacoes/configuracao-wpp/teste-departamento', [NotificacaoController::class, 'enviarTesteDepartamentoWpp'])
                ->name('notificacoes.configuracao-wpp.teste-departamento');
            Route::post('notificacoes/enviar-texto', [NotificacaoController::class, 'enviarTexto'])->name('notificacoes.enviar-texto');

            Route::get('configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes.index');
            Route::post('configuracoes/acessos', [ConfiguracaoController::class, 'atribuirAcesso'])->name('configuracoes.acessos.store');
            Route::post('configuracoes/metas', [ConfiguracaoController::class, 'salvarMetas'])->name('configuracoes.metas.store');
            Route::post('configuracoes/administradores', [ConfiguracaoController::class, 'atribuirAdministrador'])->name('configuracoes.admins.store');
            Route::resource('regionais', RegionalController::class)
                ->except(['show'])
                ->parameters(['regionais' => 'regional']);
            Route::resource('cargos', CargoController::class)->except(['show']);
            Route::resource('membros', MembroController::class)->except(['show']);
        });

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});
