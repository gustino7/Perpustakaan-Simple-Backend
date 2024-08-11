@extends('layouts')

@section('content')

    <div class="container mt-5">
        <div id="error-message" class="alert alert-danger mt-3 d-none"></div>
        <div id="success-message" class="alert alert-success mt-3 d-none"></div>
        <h2>Tambah Buku</h2>
        <form id="buku-form" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" class="form-control" id="judul" name="judul" required>
            </div>
            <div class="mb-3">
                <label for="kategoris_id" class="form-label">Kategori</label>
                <select class="form-select" id="kategoris_id" name="kategoris_id" required>
                    <option value="">Pilih Kategori</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" required></textarea>
            </div>
            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah</label>
                <input type="number" class="form-control" id="jumlah" name="jumlah" required>
            </div>
            <div class="mb-3">
                <label for="file" class="form-label">File Buku (PDF)</label>
                <input type="file" class="form-control" id="file" name="file" accept="application/pdf" required>
            </div>
            <div class="mb-3">
                <label for="cover" class="form-label">Cover (PNG, JPG, JPEG)</label>
                <input type="file" class="form-control" id="cover" name="cover" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Buku</button>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            let userId = null;
            let token = localStorage.getItem('access_token');
            $.ajax({
                url: '/api/user',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function (response) {
                    userId = response.id;
                },
                error: function (xhr) {
                    console.log('Gagal mendapatkan User ID:', xhr.responseJSON.message);
                }
            });

            $.ajax({
                url: '/api/kategori',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function (response) {
                    response.data.forEach(function (kategori) {
                        $('#kategoris_id').append(`<option value="${kategori.id}">${kategori.nama}</option>`);
                    });
                },
                error: function (xhr) {
                    console.log('Gagal mendapatkan data kategori:', xhr.responseJSON.message);
                }
            });

            $('#buku-form').on('submit', function (e) {
                e.preventDefault();
                let fileInput = $('#file')[0].files[0];
                let coverInput = $('#cover')[0].files[0];
                var formData = new FormData(this);
                
                formData.append('judul', $('#judul').val());
                formData.append('user_id', userId);
                formData.append('kategoris_id', $('#kategoris_id').val());
                formData.append('deskripsi', $('#deskripsi').val());
                formData.append('jumlah', $('#jumlah').val());
                formData.append('file', fileInput);
                formData.append('cover', coverInput);

                $.ajax({
                    url: '/api/bukus',
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
                        $('#buku-form').trigger("reset");
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