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
    public function index()//admin
    {
        return view('books.index');
    }
    public function siswaIndex()//siswa
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
     * search admin.
     */
    public function fetch(Request $request)
    {
        $search = $request->query('search');

        $books = Book::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($books);
    }

    /**
     * Simpan buku baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'kelas' => 'required|string',
            'category' => 'required|string',
            'publication_date' => 'required|date',
            'description' => 'required|string',
            'file' => 'required|mimes:pdf,epub|max:2048',
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        // Cek apakah file dengan nama yang sama sudah ada di penyimpanan
        if (Storage::disk('public')->exists('books/' . $filename)) {
            return response()->json([
                'errors' => ['file' => ['File dengan nama tersebut sudah ada. Silakan ubah nama file atau pilih file lain.']]
            ], 422);
        }

        // Simpan file
        $filePath = $file->storeAs('books', $filename, 'public');

        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'kelas' => $request->kelas,
            'category' => $request->category,
            'publication_date' => $request->publication_date,
            'description' => $request->description,
            'file_path' => $filePath,
        ]);

        return response()->json(['message' => 'Buku berhasil disimpan.']);
    }


    // pencarian buku siswa
    public function json(Request $request)
    {
        $query = $request->input('search');
        $kelas = $request->input('kelas');

        $books = Book::when($kelas, fn($q) => $q->where('kelas', $kelas))
            ->when($query, fn($q) =>
                $q->where('title', 'like', "%{$query}%")
                ->orWhere('author', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
            )
            ->latest()
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
            'kelas' => $book->kelas,
            'publication_date' => $book->publication_date,
            'category' => $book->category,
            'description' => $book->description,
            'file_url' => asset('storage/' . $book->file_path) // URL file PDF
        ]);
    }

   public function edit($id)
    {
        $book = Book::findOrFail($id);
        return view('books.show', compact('book'));
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        // Validasi input
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'kelas' => 'required|string',
            'category' => 'required|string',
            'publication_date' => 'required|date',
            'description' => 'required|string',
            'file' => 'nullable|mimes:pdf,epub|max:2048',
        ]);

        // Siapkan data untuk update
        $book->title = $request->title;
        $book->author = $request->author;
        $book->kelas = $request->kelas;
        $book->category = $request->category;
        $book->publication_date = $request->publication_date;
        $book->description = $request->description;

        // Cek jika ada file baru di-upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();

            // Cek file duplikat di storage
            if (Storage::disk('public')->exists('books/' . $filename)) {
                return response()->json([
                    'errors' => ['file' => ['File dengan nama tersebut sudah ada. Ganti nama file atau pilih file lain.']]
                ], 422);
            }

            // Hapus file lama jika ada
            if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                Storage::disk('public')->delete($book->file_path);
            }

            // Simpan file baru
            $filePath = $file->storeAs('books', $filename, 'public');
            $book->file_path = $filePath;
        }

        $book->save();

        return response()->json(['message' => 'Buku berhasil diperbarui.']);
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
