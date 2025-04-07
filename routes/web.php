<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController;

use Illuminate\Support\Facades\Route;


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
Route::get('/test', function () {
    return view('books.test');
});

// Halaman utama daftar buku
Route::get('/books', [BookController::class, 'index'])->name('books.index');

// Halaman tambah buku
Route::get('/books/create', [BookController::class, 'create'])->name('books.create');

// API untuk mengambil semua buku (untuk AJAX)
Route::get('/books/fetch', [BookController::class, 'fetch'])->name('books.fetch');

// API untuk menyimpan buku baru
Route::post('/books', [BookController::class, 'store'])->name('books.store');

// API untuk melihat detail buku
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');

//search
Route::get('/books/search', [BookController::class, 'json'])->name('siswa.books.json');
Route::get('/siswa/books/json', [BookController::class, 'json'])->name('siswa.books.json');

// API untuk memperbarui buku
Route::put('/books/{id}', [BookController::class, 'update'])->name('books.update');

// API untuk menghapus buku
Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
