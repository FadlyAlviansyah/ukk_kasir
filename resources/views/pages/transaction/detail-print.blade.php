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
  <div class="page-breadcrumb ">
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
  <div class="container d-flex flex-column" style="min-height: 500px">
    <div class="row bg-light px-3 py-4">
      <div class="col-12">
        <div class="card p-4">
          <div class="card-body">
            <div class="invoice-header">
              <div class="row">
                <div class="col-12">
                  <a href="#" class="btn btn-primary">Unduh</a>
                  <a href="{{ route('transaction.home') }}" class="btn btn-secondary">Kembali</a>
                </div>
              </div>
              <div class="row mt-3">
                <div class="col-lg-9 col-md-12">
                  <div class="invoice-details">
                    @if ($transaction->member)
                      <address>
                        <b>{{ $transaction->member->name }}</b>
                        <br>
                        MEMBER SEJAK : {{ $transaction->member->created_at->translatedFormat('d F Y') }}
                        <br>
                        MEMBER POIN : {{ $transaction->member->points }}
                      </address>
                    @endif
                  </div>
                </div>
                <div class="col-lg-3 col-md-12">
                  <div class="invoice-details">
                    <div>Invoice - #{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</div>
                    <div>{{ $transaction->created_at->translatedFormat('d F Y') }}</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="invoice-body">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">Produk</th>
                          <th scope="col">Harga</th>
                          <th scope="col">Kuantitas</th>
                          <th scope="col">Sub Total</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($transaction->transactionDetail as $item)
                          <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>Rp. {{ number_format($item->product->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp. {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="invoice-footer">
              <div class="row">
                <div class="col-lg-9 col-md-12 bg-light px-4 py-3">
                  <div class="row">
                    <div class="col-lg-3 col-12">
                      <div class="row">
                        <small>POIN DIGUNAKAN</small>
                      </div>
                      <div class="row">
                        <span class="fw-semibold">{{ $transaction->points_used }}</span>
                      </div>
                    </div>
                    <div class="col-lg-3 col-12">
                      <div class="row">
                        <small>KASIR</small>
                      </div>
                      <div class="row">
                        <span class="fw-semibold">{{ $transaction->user->name }}</span>
                      </div>
                    </div>
                    <div class="col-lg-3 col-12">
                      <div class="row">
                        <small>TOTAL BAYAR</small>
                      </div>
                      <div class="row">
                        <span class="fw-semibold">Rp. {{ number_format($transaction->amount_paid, 0, ',', '.') }}</span>
                      </div>
                    </div>
                    <div class="col-lg-3 col-12">
                      <div class="row">
                        <small>KEMBALIAN</small>
                      </div>
                      <div class="row">
                        <span class="fw-semibold">Rp. {{ number_format($transaction->amount_change, 0, ',', '.') }}</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-12 bg-dark d-flex px-4 py-3">
                  <small class="text-light">TOTAL</small>
                  <span class="fw-semibold fs-1 text-white ms-3">Rp. {{ number_format($transaction->total, 0, ',', '.') }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection