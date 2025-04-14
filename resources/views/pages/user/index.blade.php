@extends('layouts.main')

@section('title', 'User')

@section('header')
    @include('components.header')
@endsection

@section('aside')
    @include('components.aside')
@endsection

@section('content')
  @if (Session::get('added'))
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        showBasicAlert('', 'Data user berhasil ditambahkan!', 'success');
      })
    </script>
  @endif
  @if (Session::get('updated'))
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        showBasicAlert('', 'Data user berhasil diubah!', 'success');
      })
    </script>
  @endif
  @if (Session::get('deleted'))
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        showBasicAlert('', 'Data user berhasil dihapus!', 'success');
      })
    </script>
  @endif
  @if (Session::get('error'))
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        showBasicAlert('', 'Data user sudah terkait dengan transaksi!', 'error');
      })
    </script>
  @endif
  <div class="page-wrapper">
    <div class="page-breadcrumb">
      <div class="row align-items-center">
        <div class="col-6">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 d-flex align-items-center">
              <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="link"><i class="mdi mdi-home-outline fs-4"></i></a></li>
              <li class="breadcrumb-item active" aria-current="page">User</li>
            </ol>
          </nav>
          <h1 class="mb-0 fw-bold">User</h1> 
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="row justify-content-end">
                <div class="col text-start">
                  <a href="{{ route('user.create') }}" class="btn btn-info">Export User (.xlsx)</a>
                </div>
                <div class="col text-end">
                  <a href="{{ route('user.create') }}" class="btn btn-primary">Tambah User</a>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Email</th>
                      <th scope="col">Nama</th>
                      <th scope="col">Role</th>
                      <th scope="col"></th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                        $i = 1
                    @endphp
                    @foreach ($users as $user)
                      <tr>
                        <th>{{ $i++ }}</th>
                        <td>{{ $user['email'] }}</td>
                        <td>{{ $user['name'] }}</td>
                        <td class="text-capitalize">{{ $user['role'] }}</td>
                        <td>
                          <div class="d-flex justify-content-around">
                            <a href="{{ route('user.edit', $user['id']) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('user.destroy', $user['id']) }}" method="post">
                              @csrf
                              @method('delete')
                              <button class="btn btn-danger" onclick="showDeleteConfirmationAlert(event, this.form)">Hapus</button>
                            </form>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection