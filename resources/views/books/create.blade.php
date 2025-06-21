@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">📘 Tambah Buku Digital</h2>

    <form id="uploadForm" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">
        @csrf

        <!-- Error Alert -->
        <div id="errorAlert" class="alert alert-danger d-none">
            <strong>Terjadi kesalahan:</strong>
            <ul id="errorList" class="mb-0 mt-2"></ul>
        </div>

        <div class="mb-3">
            <label class="form-label"><i class="bi bi-book"></i> Judul Buku</label>
            <input type="text" name="title" class="form-control" placeholder="Masukkan judul buku" required>
        </div>

        <div class="mb-3">
            <label class="form-label"><i class="bi bi-person"></i> Penulis</label>
            <input type="text" name="author" class="form-control" placeholder="Nama penulis" required>
        </div>

        <div class="mb-3">
            <label class="form-label"><i class="bi bi-mortarboard"></i> Kelas Rekomendasi</label>
            <select name="kelas" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                <option value="7">Kelas 7</option>
                <option value="8">Kelas 8</option>
                <option value="9">Kelas 9</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label"><i class="bi bi-tags"></i> Kategori</label>
            <select name="category" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Fiksi">📖 Fiksi</option>
                <option value="Non-Fiksi">📚 Non-Fiksi</option>
                <option value="Pelajaran">📘 Pelajaran</option>
                <option value="Komik">🎨 Komik</option>
                <option value="Biografi">👤 Biografi</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label"><i class="bi bi-calendar3"></i> Tanggal Terbit</label>
            <input type="date" name="publication_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label"><i class="bi bi-pencil-square"></i> Deskripsi</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi singkat tentang isi buku" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label"><i class="bi bi-upload"></i> Upload File Buku (PDF/EPUB)</label>
            <input type="file" name="file" class="form-control" required>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
            <a href="{{ route('books.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#uploadForm').on('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: '{{ route('books.store') }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function () {
                    alert("✅ Buku berhasil ditambahkan!");
                    $('#uploadForm')[0].reset();
                    $('#errorAlert').addClass('d-none');
                    window.location.href = "{{ route('books.index') }}";
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorList = '';
                        $.each(errors, function (key, value) {
                            errorList += '<li>' + value[0] + '</li>';
                        });
                        $('#errorList').html(errorList);
                        $('#errorAlert').removeClass('d-none');
                    }
                }
            });
        });
    });
</script>
@endsection
