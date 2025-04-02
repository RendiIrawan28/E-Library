@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 id="book-title">Baca Buku</h1>
        <iframe id="book-frame" width="100%" height="600px" style="border: none;"></iframe>
        <a href="/books" class="btn btn-secondary mt-3">Kembali</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let bookId = window.location.pathname.split("/").slice(-2, -1)[0];

            fetch(`/api/books/${bookId}`)
                .then(response => response.json())
                .then(book => {
                    if (book.file_url) {
                        document.getElementById("book-frame").src = book.file_url;
                        document.getElementById("book-title").textContent = "Baca Buku: " + book.title;
                    } else {
                        alert("Buku tidak ditemukan atau file tidak tersedia.");
                        window.location.href = "/books";
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
@endsection
