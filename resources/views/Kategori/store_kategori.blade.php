@extends('layouts')

@section('content')

    <div class="container mt-5">
        <div id="error-message" class="alert alert-danger mt-3 d-none"></div>
        <div id="success-message" class="alert alert-success mt-3 d-none"></div>
        <h2>Tambah Kategori</h2>
        <form id="kategori-form">
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <input type="text" class="form-control" id="kategori" name="kategori" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Kategori</button>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            let token = localStorage.getItem('access_token');

            $('#kategori-form').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);

                formData.append('nama', $('#kategori').val());

                $.ajax({
                    url: '/api/kategori',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $('#success-message').removeClass('d-none').text(response.message);
                        $('#error-message').addClass('d-none');
                        $('#kategori-form').trigger("reset");
                    },
                    error: function (xhr) {
                        $('#error-message').removeClass('d-none').text(xhr.responseJSON.message);
                        $('#success-message').addClass('d-none');
                    }
                });
            });
        });
    </script>

@endsection