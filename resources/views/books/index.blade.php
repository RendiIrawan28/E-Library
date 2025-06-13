@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Daftar Buku</h1>
        <a href="{{ route('books.create') }}" class="btn btn-primary">Tambah Buku</a>

        <!-- Input Pencarian -->
        <input type="text" id="search" class="form-control mt-3"
            placeholder="Cari buku berdasarkan judul, penulis, atau kategori...">

        <!-- Tabel Buku -->
        <table class="table table-striped mt-3">
            @csrf
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="bookList">
                <!-- Data akan dimuat oleh JavaScript -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Panggil fetchBooks saat halaman dimuat
            fetchBooks();
            // Fungsi untuk mengambil daftar buku dari method fetch()
            function fetchBooks() {
                $.ajax({
                    url: '{{ route('books.fetch') }}',
                    type: 'GET',
                    success: function(response) {
                        $('#bookList').empty();
                        response.forEach(function(book) {
                            $('#bookList').append(`
                                <tr>
                                    <td>${book.title}</td>
                                    <td>${book.author}</td>
                                    <td>${book.category}</td>
                                    <td>
                                        <a href="/storage/${book.file_path}" class="btn btn-info btn-sm" target="_blank">View PDF</a>
                                        <button onclick="location.href='/books/${book.id}/edit'" class="btn btn-warning btn-sm">Edit</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteBook(${book.id})">Hapus</button>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                });
            }

            // Fungsi untuk menghapus buku
            window.deleteBook = function(id) {
                if (confirm("Apakah Anda yakin ingin menghapus buku ini?")) {
                    $.ajax({
                        url: '/books/' + id,
                        type: 'DELETE',
                        success: function() {
                            alert("Buku berhasil dihapus!");
                            fetchBooks();
                        }
                    });
                }
            }

        });
    </script>
@endsection
