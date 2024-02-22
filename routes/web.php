<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CacImportController;

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

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('leads', [LeadsController::class, 'index']);
Route::get('export-leads', [ExportController::class, 'exportLeads'])->name('exportLeads');

// Route to export WV data
Route::get('export-csv/{st}/{nd}', [ExportController::class, 'exportToCsv'])->name('download-csv');

// Route to export CAC data
Route::get('export-cacdata/{st}/{nd}', [ExportController::class, 'exportCsvData'])->name('download-cacdata');
Route::get('import-csv', [ImportController::class, 'importCsv'])->name('import.csv');
Route::get('savedata/{name}/{owner}', [ImportController::class, 'saveFile'])->name('store-file');
Route::post('show-csv', [ImportController::class, 'showCsvData'])->name('show.csv');
Route::get('cac', [CacImportController::class, 'index'])->name('cac');
Route::get('remove', [LeadsController::class, 'removeLeads'])->name('removeleads');
Route::get('removecac/{st}/{nd}', [LeadsController::class, 'removecac'])->name('remove-cac');
Route::get('removewv/{st}/{nd}', [LeadsController::class, 'removewv'])->name('remove-wv');