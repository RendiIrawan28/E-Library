@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Buku</h1>
    <form id="editForm" enctype="multipart/form-data">
        @csrf

        <input type="hidden" id="book_id" value="{{ $book->id }}">

        <div class="mb-3">
            <label>Judul</label>
            <input type="text" name="title" class="form-control" value="{{ $book->title }}">
        </div>
        <div class="mb-3">
            <label>Penulis</label>
            <input type="text" name="author" class="form-control" value="{{ $book->author }}">
        </div>
        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas Rekomendasi</label>
            <select name="kelas" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                <option value="7">Kelas 7</option>
                <option value="8">Kelas 8</option>
                <option value="9">Kelas 9</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Kategori</label>
            <select name="category" class="form-select" required>
                <option value="Fiksi" {{ $book->category == 'Fiksi' ? 'selected' : '' }}>📖 Fiksi</option>
                <option value="Non-Fiksi" {{ $book->category == 'Non-Fiksi' ? 'selected' : '' }}>📚 Non-Fiksi</option>
                <option value="Pelajaran" {{ $book->category == 'Pelajaran' ? 'selected' : '' }}>📘 Pelajaran</option>
                <option value="Komik" {{ $book->category == 'Komik' ? 'selected' : '' }}>🧑‍🎨 Komik</option>
                <option value="Biografi" {{ $book->category == 'Biografi' ? 'selected' : '' }}>👤 Biografi</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="publication_date" class="form-label">Tanggal Terbit</label>
            <input type="date" name="publication_date" class="form-control"
                   value="{{ $book->publication_date ? $book->publication_date->format('Y-m-d') : '' }}" required>
        </div>
        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control">{{ $book->description }}</textarea>
        </div>
        <div class="mb-3">
            <label>File Buku (PDF/EPUB)</label>
            <input type="file" name="file" class="form-control">
            @if ($book->file_path)
                <p class="mt-2"><a href="{{ asset('storage/' . $book->file_path) }}" target="_blank">Lihat File Lama</a></p>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('books.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        const id = $('#book_id').val();

        $.ajax({
            url: '/books/' + id,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert("Buku berhasil diperbarui!");
                window.location.href = "{{ route('books.index') }}";
            },
            error: function(xhr) {
                alert("Gagal memperbarui buku.");
                console.error(xhr.responseText);
            }
        });
    });
</script>
@endsection
