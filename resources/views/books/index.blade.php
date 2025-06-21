@extends('layouts.app')

@section('content')
<div class="container">
    <h1>📚 Daftar Buku</h1>
    <a href="{{ route('books.create') }}" class="btn btn-primary">➕ Tambah Buku</a>

    <!-- Input Pencarian -->
    <input type="text" id="search" class="form-control mt-3" placeholder="🔍 Cari berdasarkan judul, penulis, atau kategori...">

    <!-- Spinner loading -->
    <div id="loading" class="text-center my-4 d-none">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2">Memuat data buku...</p>
    </div>

    <!-- Tabel Buku -->
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Kelas</th>
                <th>Kategori</th>
                <th>Tanggal Terbit</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="bookList">
            <!-- Data akan dimuat oleh JavaScript -->
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Panggil saat load
        fetchBooks();

        // Panggil ulang saat mengetik
        $('#search').on('input', function () {
            fetchBooks($(this).val());
        });

        // Ambil data dari server
        function fetchBooks(query = '') {
            $('#loading').removeClass('d-none');
            $('#bookList').empty();

            $.ajax({
                url: '{{ route('books.fetch') }}',
                type: 'GET',
                data: { search: query },
                success: function (response) {
                    $('#bookList').empty();
                    if (response.length === 0) {
                        $('#bookList').append('<tr><td colspan="6" class="text-center text-muted">📭 Tidak ada buku ditemukan.</td></tr>');
                    } else {
                        response.forEach(function (book) {
                            const date = new Date(book.publication_date).toLocaleDateString('id-ID');
                            $('#bookList').append(`
                                <tr>
                                    <td>${book.title}</td>
                                    <td>${book.author}</td>
                                    <td>${book.kelas ?? '-'}</td>
                                    <td>${book.category}</td>
                                    <td>${date}</td>
                                    <td>
                                        <a href="/storage/${book.file_path}" class="btn btn-info btn-sm" target="_blank">📖 Lihat</a>
                                        <button onclick="location.href='/books/${book.id}/edit'" class="btn btn-warning btn-sm">✏️ Edit</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteBook(${book.id})">🗑️ Hapus</button>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                },
                error: function () {
                    $('#bookList').html('<tr><td colspan="6" class="text-danger text-center">❌ Gagal memuat data.</td></tr>');
                },
                complete: function () {
                    $('#loading').addClass('d-none');
                }
            });
        }

        // Hapus Buku
        window.deleteBook = function (id) {
            if (confirm("Apakah Anda yakin ingin menghapus buku ini?")) {
                $.ajax({
                    url: '/books/' + id,
                    type: 'DELETE',
                    success: function () {
                        alert("✅ Buku berhasil dihapus!");
                        fetchBooks($('#search').val());
                    },
                    error: function () {
                        alert("❌ Gagal menghapus buku.");
                    }
                });
            }
        }
    });
</script>
@endsection
