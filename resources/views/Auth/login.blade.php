@extends('layouts')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Login</h2>
    <div class="alert alert-danger d-none" id="error-message"></div>
    <form id="login-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>

<!-- Bootstrap and jQuery JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        $('#login-form').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '/api/login',
                method: 'POST',
                data: {
                    email: $('#email').val(),
                    password: $('#password').val()
                },
                success: function (response) {
                    alert('Login berhasil');
                    localStorage.setItem('access_token', response.access_token);
                    window.location.href = '/list-buku';
                },
                error: function (xhr) {
                    $('#error-message').removeClass('d-none').text(xhr.responseJSON.message);
                }
            });
        });
    });
</script>
@endsection