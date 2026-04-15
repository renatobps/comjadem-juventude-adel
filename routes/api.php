<?php

use App\Http\Controllers\PreInscricaoController;
use Illuminate\Support\Facades\Route;

Route::post('/inscricoes', [PreInscricaoController::class, 'store']);
