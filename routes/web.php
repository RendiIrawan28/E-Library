<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Auth;

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
    return view('auth.login');
});

// Manajemen Buku (CRUD)

Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
Route::post('/books', [BookController::class, 'store'])->name('books.store');
Route::get('/books/fetch', [BookController::class, 'fetch'])->name('books.fetch'); // AJAX
Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');

Route::post('/books/update/{id}', [BookController::class, 'update'])->name('books.update');

// Redirect setelah login
Route::get('/redirect', function () {
    $user = Auth::user();
    return match ($user->role) {
        'admin' => redirect('/admin/dashboard'),
        'siswa' => redirect('/siswa/books'),
        default => abort(403, 'Role tidak dikenali.'),
    };
})->middleware(['auth'])->name('redirect');


// =================== AUTH DAN PROFILE ===================
Route::middleware(['auth', 'verified'])->group(function () {
    // Halaman dashboard umum
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// =================== ADMIN ROUTE ===================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
   
    Route::get('/dashboard', function () {
        return view('books.index');
    })->name('admin.dashboard');


    
});


// =================== SISWA ROUTE ===================
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->group(function () {
    // Daftar buku dengan AJAX + Search
    Route::get('/books', [BookController::class, 'siswaIndex'])->name('siswa.books.index');
    Route::get('/books/json', [BookController::class, 'json'])->name('siswa.books.json');

    // Membaca buku
    Route::get('/books/{id}/read', [BookController::class, 'read'])->name('siswa.books.read');
});

require __DIR__.'/auth.php';
