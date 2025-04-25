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
                        $('#editId').val(book.id);
                        $('#editTitle').val(book.title);
                        $('#editAuthor').val(book.author);
                        $('#editCategory').val(book.category);
                        $('#editDescription').val(book.description);
                        $('#editBookModal').modal('show');
                    }
                });
            }

            // Submit form update buku
            $('#editBookForm').on('submit', function(e) {
                e.preventDefault();

                var id = $('#editId').val();
                var formData = new FormData(this);

                $.ajax({
                    url: '/books/edit/' + id,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function() {
                        alert('Buku berhasil diperbarui!');
                        $('#editBookModal').modal('hide');
                        fetchBooks(); // reload data
                    },
                    error: function(xhr) {
                        alert('Gagal memperbarui buku: ' + xhr.responseJSON.message);
                    }
                });
            });

        });
    </script>

    <!-- Modal Edit Buku -->
    <div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editBookForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Buku</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editId">
                        <div class="mb-3">
                            <label for="editTitle" class="form-label">Judul</label>
                            <input type="text" name="title" class="form-control" id="editTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAuthor" class="form-label">Penulis</label>
                            <input type="text" name="author" class="form-control" id="editAuthor" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCategory" class="form-label">Kategori</label>
                            <input type="text" name="category" class="form-control" id="editCategory" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" id="editDescription" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editFile" class="form-label">Ganti File (opsional)</label>
                            <input type="file" name="file" class="form-control" id="editFile" accept=".pdf,.epub">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
