@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">📚 Katalog Buku Digital</h2>

    <!-- ======================== -->
    <!-- 🔶 Rekomendasi per Kelas -->
    <!-- ======================== -->
    <h5 class="mb-3">✨ Rekomendasi Kelas 7</h5>
    <div class="row g-3 mb-4" id="kelas7-books">
        <div class="text-muted">Memuat...</div>
    </div>

    <h5 class="mb-3">✨ Rekomendasi Kelas 8</h5>
    <div class="row g-3 mb-4" id="kelas8-books">
        <div class="text-muted">Memuat...</div>
    </div>

    <h5 class="mb-3">✨ Rekomendasi Kelas 9</h5>
    <div class="row g-3 mb-4" id="kelas9-books">
        <div class="text-muted">Memuat...</div>
    </div>

    <!-- ======================== -->
    <!-- 🔍 Pencarian Umum -->
    <!-- ======================== -->
    <div class="input-group my-4">
        <input type="text" id="search" class="form-control" placeholder="🔍 Cari buku apa saja...">
        <span class="input-group-text bg-primary text-white"><i class="bi bi-search"></i></span>
    </div>

    <div id="book-catalog" class="row g-4">
        <div class="text-center text-muted">Masukkan kata kunci pencarian untuk menampilkan hasil.</div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .book-card {
        transition: 0.2s;
    }
    .book-card:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const catalog = document.getElementById('book-catalog');
    const searchInput = document.getElementById('search');

    // Panggil rekomendasi awal
    fetchBooksByKelas(7, 'kelas7-books');
    fetchBooksByKelas(8, 'kelas8-books');
    fetchBooksByKelas(9, 'kelas9-books');

    // Pencarian AJAX
    searchInput.addEventListener('input', function () {
        const query = this.value;
        fetch(`/siswa/books/json?search=${query}`)
            .then(res => res.json())
            .then(data => {
                catalog.innerHTML = '';
                if (data.length === 0) {
                    catalog.innerHTML = `<div class="text-muted">📭 Tidak ditemukan hasil pencarian.</div>`;
                    return;
                }
                data.forEach(book => {
                    catalog.innerHTML += renderCard(book);
                });
            });
    });

    // Ambil buku berdasarkan kelas
    function fetchBooksByKelas(kelas, containerId) {
        fetch(`/siswa/books/json?kelas=${kelas}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById(containerId);
                container.innerHTML = '';
                if (data.length === 0) {
                    container.innerHTML = `<div class="text-muted">Belum ada buku rekomendasi untuk kelas ${kelas}.</div>`;
                    return;
                }
                data.forEach(book => {
                    container.innerHTML += renderCard(book);
                });
            });
    }

    // Template card
    function renderCard(book) {
        return `
            <div class="col-md-4">
                <div class="card book-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">${book.title}</h5>
                        <p class="card-subtitle mb-2 text-muted">✍️ ${book.author}</p>
                        <span class="badge bg-secondary mb-2">${book.category}</span>
                        <p class="card-text small">${book.description.substring(0, 80)}...</p>
                        <a href="/storage/${book.file_path}" target="_blank" class="btn btn-sm btn-primary mt-auto">📖 Baca</a>
                    </div>
                </div>
            </div>
        `;
    }
});
</script>
@endpush
