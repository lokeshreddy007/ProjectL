<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
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

// Upload File
Route::get('/upload_file', [UploadController::class, 'index']);
Route::post('/upload-doc-file', [UploadController::class, 'uploadToServer']);
