<?php

use App\Http\Controllers\Api\BukuController;
use Illuminate\Support\Facades\Route;

Route::get('/register', function () {
    return view('Auth/register');
});

Route::get('/login', function () {
    return view('Auth/login');
});

Route::get('/list-buku', function () {
    return view('Buku/list_buku');
});

Route::get('/store-buku', function () {
    return view('Buku/store_buku');
});

Route::get('/list-kategori', function () {
    return view('Kategori/list_kategori');
});

Route::get('/store-kategori', function () {
    return view('Kategori/store_kategori');
});

Route::get('/export-xls', [BukuController::class, 'exportXls'])->name('export.buku');
Route::get('/export-pdf', [BukuController::class, 'exportPdf'])->name('export.pdf');