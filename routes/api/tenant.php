<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\RowController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ValueController;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;

Route::get('/', function () {
    return response()->json([
        'message' => 'Kayabase API',
        'status' => 'success',
        'version' => AppServiceProvider::VERSION,
        'docs' => url('http://api-docs.kayabase.com')
    ]);
});

Route::middleware([
    'api',
    'tenant_validation',
    InitializeTenancyByRequestData::class,
])->group(function () {
    Route::delete('logout', [AuthenticatedController::class, 'destroy']);

    Route::apiResources([
        'databases' => DatabaseController::class,
        'tables' => TableController::class,
        'tables/{table}/rows' => RowController::class,
        'tables/{table}/columns' => ColumnController::class,
        'values' => ValueController::class,
    ]);

    Route::post('tables/{table}/requests', [RequestController::class, 'store']);
    Route::get('requests/{query}', [RequestController::class, 'show']);
});
