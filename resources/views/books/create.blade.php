@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Buku</h1>
    <form id="uploadForm">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Judul Buku</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="author" class="form-label">Penulis</label>
            <input type="text" name="author" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Kategori</label>
            <input type="text" name="category" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="file" class="form-label">Upload File Buku (PDF/EPUB)</label>
            <input type="file" name="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('books.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: "/books",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    alert("buku berhasil ditambahkan")
                    fetchPDFs();
                    $('#uploadForm')[0].reset();
                }
            });
        });
    });
</script>
@endsection