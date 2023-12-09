<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UploadController;

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


Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {
    route::get('/', function () {
        return redirect()->route('files.index');
    });
    route::resources(['files' => FileController::class]);
});

Route::get('download/{token}', [FileController::class, 'download'])->name('download');

Route::get('upload', [UploadController::class, 'index'])->name('upload.index');
Route::post('upload', [UploadController::class, 'store'])->name('upload.store');
