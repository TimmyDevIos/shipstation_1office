<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIendpoints\Webhooks\WebhookController;
use App\Http\Controllers\_1Office_ShipStation\OrderController;
use App\Http\Controllers\History\HistoryController;
use App\Http\Controllers\Alert\AlertController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EnvController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('translations/{locale}', function ($locale) {
    $path = resource_path("lang/{$locale}.json");
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->json(json_decode(file_get_contents($path), true));
});

Route::get('/webhook-logs', [WebhookController::class, 'showLogs']);

Auth::routes();
Route::get('/', [OrderController::class, 'index'])->name('home');
Route::get('/orders', [OrderController::class, 'index'])->name('index');
Route::get('/table', [OrderController::class, 'table'])->name('table');
Route::get('/history', [HistoryController::class, 'index'])->name('index');

Route::prefix('alert')->group(function () {
    Route::get('/', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/status/{id}/{status}', [AlertController::class, 'update'])->name("alerts.updateStatus");
});

Route::get('/api-1office', [EnvController::class, 'show1Office'])->name("api-1office.index");
Route::post('/update-api-1office', [EnvController::class, 'update1Office']);
