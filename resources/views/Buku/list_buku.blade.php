@extends('layouts')

@section('content')

    <div class="container mt-5">
        <h2 class="mb-4">Daftar Buku</h2>

        <div class="alert alert-danger d-none" id="error-message"></div>

        <div class="container mt-5">
            <a href="{{ route('export.buku') }}" class="btn btn-success mb-3">Export ke Excel</a>
        </div>
        <div class="container mb-5">
            <a href="{{ route('export.pdf') }}" class="btn btn-success mb-3">Export ke Pdf</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="buku-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                        <th>File</th>
                        <th>Cover</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $.ajax({
                url: '/api/bukus',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('access_token')
                },
                success: function (response) {

                    if (response.status === 200) {
                        var bukuData = response.data.data;
                        var bukuTable = $('#buku-table tbody');
                        bukuTable.empty();

                        $.each(bukuData, function (index, buku) {
                            bukuTable.append(
                                '<tr>' +
                                '<td>' + buku.id + '</td>' +
                                '<td>' + buku.judul + '</td>' +
                                '<td>' + buku.kategoris_id + '</td>' +
                                '<td>' + buku.deskripsi + '</td>' +
                                '<td>' + buku.jumlah + '</td>' +
                                '<td>' + buku.file + '</td>' +
                                '<td>' + buku.cover + '</td>' +
                                '</tr>'
                            );
                        });
                    } else {
                        $('#error-message').removeClass('d-none').text(response.message);
                    }
                },
                error: function (error) {
                    $('#error-message').removeClass('d-none').text("Gagal mendapatkan data");
                }
            });
        });
    </script>

@endsection