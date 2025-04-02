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
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|mimes:pdf,epub|max:2048'
        ]);

        // Simpan file ke storage
        $filePath = $request->file('file')->store('books', 'public');

        // Simpan data buku ke database
        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'category' => $request->category,
            'description' => $request->description ?? '',
            'file_path' => $filePath
        ]);

        return response()->json(['message' => 'Buku berhasil ditambahkan', 'book' => $book], 201);
    }

    // pencarian buku
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Cari buku berdasarkan title, author, atau category
        $book = Book::where('title', 'LIKE', "%$query%")
            ->orWhere('author', 'LIKE', "%$query%")
            ->orWhere('category', 'LIKE', "%$query%")
            ->get();

        return response()->json($book);
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
        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|mimes:pdf,epub|max:2048'
        ]);

        // Perbarui data buku
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'category' => $request->category,
            'description' => $request->description ?? $book->description
        ]);

        // Jika ada file baru, hapus yang lama dan simpan yang baru
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($book->file_path);
            $filePath = $request->file('file')->store('books', 'public');
            $book->update(['file_path' => $filePath]);
        }

        return response()->json(['message' => 'Buku berhasil diperbarui', 'book' => $book]);
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
