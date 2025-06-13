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
                                        <button class="btn btn-warning btn-sm" onclick="editBook(${book.id})">Edit</button>
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

            // Buka modal edit dan isi datanya
            window.editBook = function(id) {
                $.ajax({
                    url: '/books/' + id,
                    type: 'GET',
                    success: function(book) {
                        $('#edit_book_id').val(book.id);
                        $('#edit_title').val(book.title);
                        $('#edit_author').val(book.author);
                        $('#edit_category').val(book.category);
                        $('#edit_description').val(book.description);
                        $('#editModal').modal('show');
                    }
                });
            }

            // Submit form update buku
            $('#editBookForm').on('submit', function(e) {
                e.preventDefault();

                let id = $('#edit_book_id').val();
                let formData = new FormData(this);

                $.ajax({
                    url: `/books/update/${id}`,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        if (res.success) {
                            $('#editModal').modal('hide');
                            alert(res.message);
                            fetchBooks();
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan saat menyimpan.");
                    }
                });
            });

        });
    </script>

    <!-- Modal Edit Buku -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editBookForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Buku</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="book_id" id="edit_book_id">
                        <div class="mb-3">
                            <label>Judul</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Penulis</label>
                            <input type="text" name="author" id="edit_author" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Kategori</label>
                            <input type="text" name="category" id="edit_category" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea name="description" id="edit_description" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>File (PDF/EPUB)</label>
                            <input type="file" name="file" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
