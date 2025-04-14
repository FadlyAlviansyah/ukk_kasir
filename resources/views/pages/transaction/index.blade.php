@extends('layouts.main')

@section('title', 'Penjualan')

@section('header')
  @include('components.header')
@endsection

@section('aside')
  @include('components.aside')
@endsection

@section('content')
  <div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 d-flex align-items-center">
                      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="link"><i class="mdi mdi-home-outline fs-4"></i></a></li>
                      <li class="breadcrumb-item active" aria-current="page">Penjualan</li>
                    </ol>
                  </nav>
                <h1 class="mb-0 fw-bold">Penjualan</h1> 
            </div>
        </div>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="row justify-content-end mb-3">
                <div class="col text-start">
                  <a href="{{ route('transaction.exportExcel') }}" class="btn btn-info">Export Penjualan (.xlsx)</a>
                </div>
                @if (Auth::user()->role === 'cashier')
                  <div class="col text-end">
                    <a href="{{ route('transaction.create') }}" class="btn btn-primary">Tambah Penjualan</a>
                  </div>
                @endif
              </div>
              <div class="table-responsive">
                <table id="salesTable" class="table">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Nama Pelanggan</th>
                      <th scope="col">Tanggal Penjualan</th>
                      <th scope="col">Total Harga</th>
                      <th scope="col">Dibuat Oleh</th>
                      @if(Auth::user()->role === 'cashier')
                        <th scope="col"></th>
                      @endif
                    </tr>
                  </thead>
                  <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($transactions as $transaction)
                      <tr>
                        <td class="text-start">{{ $i++ }}</td>
                        <td>{{ $transaction->member ? $transaction->member->name : 'Non Member' }}</td>
                        <td>{{ $transaction->created_at->format('d-m-Y') }}</td>
                        <td>Rp. {{ number_format($transaction->total, 0, ',', '.') }}</td>
                        <td>{{ $transaction->user->name }}</td>
                        @if (Auth::user()->role === 'cashier')
                          <td>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#showDetailModal-{{ $transaction['id'] }}">Lihat</button>
                            <a href="{{ route('transaction.print', $transaction['id']) }}" class="btn btn-info ms-3">Unduh Bukti</a>
                            <div class="modal fade" id="showDetailModal-{{ $transaction['id'] }}" tabindex="-1" aria-labelledby="showDetailModal" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="showDetailModal">Detail Penjualan</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                    <div class="row">
                                      <div class="col-6">
                                        <small>
                                          Member Status : {{ $transaction->member ? 'Member' : 'Non Member' }}
                                          <br>
                                          No. HP : {{ $transaction->member ? $transaction->member->phone_number : '-' }}
                                          <br>
                                          Poin Member : {{ $transaction->member ? $transaction->member->points : '-' }}
                                        </small>
                                      </div>
                                      <div class="col-6">
                                        <small>
                                          Bergabung Sejak : {{ $transaction->member ? $transaction->member->created_at->translatedFormat('d F Y') : '-' }}
                                        </small>
                                      </div>
                                    </div>
                                    <div class="row mt-3">
                                      <table class="table table-borderless">
                                        <thead>
                                          <tr>
                                            <th>Nama Produk</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Sub Total</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          @foreach ($transaction->transactionDetail as $item)
                                            <tr>
                                              <td class="pt-0">{{ $item->product->name }}</td>
                                              <td class="pt-0">{{ $item->quantity }}</td>
                                              <td class="pt-0">Rp. {{ number_format($item->product->price, 0, ',', '.') }}</td>
                                              <td class="pt-0">Rp. {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                          @endforeach
                                          <tr>
                                            <th colspan="3" class="text-end">Total</th>
                                            <th>Rp. {{ number_format($transaction->total, 0, ',', '.') }}</th>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </div>
                                    <div class="row">
                                      <div class="text-center">Dibuat pada : {{ $transaction->created_at->format('Y-m-d H:i:s') }}</div>
                                      <div class="text-center">Oleh : {{ $transaction->user->name }}</div>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </td>
                        @endif
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

@push('script')
  @if (Auth::user()->role === 'cashier')
    <script>
      $(document).ready(function(){
        $('#salesTable').DataTable({
          "columnDefs": [
            {"width": "50px", "target": 0},
            {"width": "220px", "target": 5},
            {"orderable": false, "target": 5},
          ]
        });
      });
    </script>
  @else
    <script>
      $(document).ready(function(){
        $('#salesTable').DataTable({
          "columnDefs": [
            {"width": "50px", "target": 0},
          ]
        });
      });
    </script>
  @endif
@endpush