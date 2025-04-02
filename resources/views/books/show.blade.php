@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $book->title }}</h1>
    <p><strong>Penulis:</strong> {{ $book->author }}</p>
    <p><strong>Kategori:</strong> {{ $book->category }}</p>
    <p><strong>Deskripsi:</strong> {{ $book->description }}</p>
    <a href="{{ asset('storage/' . $book->file_path) }}" class="btn btn-primary" target="_blank">Baca Buku</a>
    <a href="{{ route('books.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection