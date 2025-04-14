@extends('layouts.main')

@section('title', 'Login')

@section('content')
@if (Session::get('loginError'))
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      showBasicAlert('Login Gagal', 'Pastikan email atau password sudah sesuai!', 'error');
    })
  </script>
@endif
@if (Session::get('logoutSuccess'))
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      showBasicAlert('', 'Logout berhasil!', 'success');
    })
  </script>
@endif
<div class="position-relative overflow-hidden min-vh-100 d-flex align-items-center justify-content-center">
    <div class="p-5 border w-50 rounded">
      <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-3">  
          <label for="email" class="form-label">Email address</label>
          <input type="email" name="email" class="form-control" id="email">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" class="form-control" id="password">
        </div>
        <button type="submit" class="btn btn-primary w-100">Log In</button>
      </form>
    </div>
  </div>
@endsection