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
            <label>Kategori</label>
            <input type="text" name="category" class="form-control" value="{{ $book->category }}">
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
