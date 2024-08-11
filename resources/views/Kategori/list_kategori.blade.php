@extends('layouts')

@section('content')

    <div class="container mt-5">
        <h2 class="mb-4">Daftar Kategori</h2>

        <div class="alert alert-danger d-none" id="error-message"></div>

        <div class="table-responsive">
            <table class="table table-bordered" id="kategori-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $.ajax({
                url: '/api/kategori',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('access_token')
                },
                success: function (response) {
                    
                    if (response.status === 200) {
                        var kategoriData = response.data;
                        var kategoriTable = $('#kategori-table tbody');
                        kategoriTable.empty();

                        $.each(kategoriData, function (index, kategori) {
                            kategoriTable.append(
                                '<tr>' +
                                '<td>' + kategori.id + '</td>' +
                                '<td>' + kategori.nama + '</td>' +
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