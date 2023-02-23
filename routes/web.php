<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('leads', [LeadsController::class, 'index']);
Route::get('export-leads', [ExportController::class, 'exportLeads'])->name('exportLeads');
Route::get('export-csv/{st}/{nd}', [ExportController::class, 'exportToCsv'])->name('download-csv');
Route::get('import-csv', [ImportController::class, 'importCsv'])->name('import.csv');
Route::post('store', [ImportController::class, 'saveFile'])->name('store-file');