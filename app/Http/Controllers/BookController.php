<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Tampilkan halaman utama daftar buku.
     */
    public function index()
    {
        return view('books.index');
    }
    public function siswaIndex()
    {
        return view('books.test');
    }

    /**
     * Tampilkan halaman tambah buku.
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Mengambil semua data buku untuk AJAX.
     */
    public function fetch()
    {
        $book = Book::all();
        return response()->json($book);
    }

    /**
     * Simpan buku baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'file' => 'required|file|mimes:pdf,epub|max:5120', // max 5MB
        ]);

        // Simpan file dan data buku
        $path = $request->file('file')->store('books', 'public');

        Book::create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'file_path' => $path,
        ]);

        return response()->json(['message' => 'Buku berhasil disimpan.'], 200);
    }


    // pencarian buku
    public function json(Request $request)
    {
        $query = $request->search;
        $books = Book::where('title', 'like', '%' . $query . '%')
                    ->orWhere('author', 'like', '%' . $query . '%')
                    ->get();
    
        return response()->json($books);
    }


    /**
     * Menampilkan detail buku berdasarkan ID.
     */
    public function show($id)
    {
        $book = Book::findOrFail($id);

        return response()->json([
            'title' => $book->title,
            'author' => $book->author,
            'category' => $book->category,
            'description' => $book->description,
            'file_url' => asset('storage/' . $book->file_path) // URL file PDF
        ]);
    }

    /**
     * Perbarui data buku yang ada.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'description' => 'required|string',
            'file'        => 'nullable|mimes:pdf,epub|max:5120',
        ]);

        $book = Book::findOrFail($id);

        // Jika user upload file baru, hapus file lama dan simpan yang baru
        if ($request->hasFile('file')) {
            // Hapus file lama kalau ada
            if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                Storage::disk('public')->delete($book->file_path);
            }

            // Simpan file baru
            $filePath = $request->file('file')->store('books', 'public');
            $book->file_path = $filePath;
        }

        $book->update([
            'title'       => $request->title,
            'author'      => $request->author,
            'category'    => $request->category,
            'description' => $request->description,
            'file_path'   => $book->file_path, // bisa saja tetap yang lama
        ]);

        return response()->json(['message' => 'Buku berhasil diperbarui']);
    }


    /**
     * Hapus buku dari database.
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        
        // Hapus file dari storage
        Storage::disk('public')->delete($book->file_path);

        // Hapus data dari database
        $book->delete();

        return response()->json(['message' => 'Buku berhasil dihapus']);
    }
}
