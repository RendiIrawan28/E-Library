@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Buku Digital</h2>

    <div class="mb-3">
        <input type="text" id="search" class="form-control" placeholder="Cari buku berdasarkan judul atau penulis...">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="book-list">
                <tr><td colspan="5" class="text-center">Masukkan kata kunci pencarian.</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('search').addEventListener('input', function () {
    const query = this.value;

    fetch(`/siswa/books/json?search=${query}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('book-list');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center">Buku tidak ditemukan.</td></tr>`;
                return;
            }

            data.forEach(book => {
                tbody.innerHTML += `
                    <tr>
                        <td>${book.title}</td>
                        <td>${book.author}</td>
                        <td>${book.category}</td>
                        <td>${book.description.substring(0, 50)}...</td>
                        <td>
                            <a href="/storage/${book.file_path}" class="btn btn-sm btn-primary">Baca</a>
                        </td>
                    </tr>`;
            });
        });
});
</script>
@endpush
