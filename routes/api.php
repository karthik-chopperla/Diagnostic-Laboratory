<?php

use App\Http\Controllers\TemporaryDatabaseRoleDiagnosticController;
use Illuminate\Support\Facades\Route;

Route::post('/pariksha/db-role-diagnostic', TemporaryDatabaseRoleDiagnosticController::class)
    ->name('pariksha.db-role-diagnostic');
