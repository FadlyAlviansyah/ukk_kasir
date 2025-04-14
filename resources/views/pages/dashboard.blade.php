@extends('layouts.main')

@section('title', 'Dashboard')

@section('header')
    @include('components.header')
@endsection

@section('aside')
    @include('components.aside')
@endsection

@section('content')
@if (Session::get('loginSuccess'))
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      showBasicAlert('', 'Login berhasil!', 'success');
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
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </nav>
          <h1 class="mb-0 fw-bold">Dashboard</h1> 
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="card">
          <div class="card-body">
            <div class="d-md-flex align-items-center">
              <div>
                <h4 class="card-title">Selamat Datang, {{ Auth::user()->name }}!</h4>
              </div>
            </div>
            <div class="row">
              @if (Auth::user()->role === 'admin')
                <div class="col-lg-8">
                    <canvas id="barChart"></canvas>
                </div>
                <div class="col-lg-4">
                    <canvas id="pieChart"></canvas>
                </div>
              @else
              <div class="card text-center p-0">
                <div class="card-header">
                    Total Penjualan Hari Ini
                </div>
                <div class="card-body">
                  <h5 class="card-title">{{ $totalTransactionsToday }}</h5>
                  <p class="card-text">Jumlah total penjualan yang terjadi hari ini.</p>
                </div>
                <div class="card-footer text-body-secondary">
                  Terakhir diperbarui: {{ $lastUpdated }}
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@if(Auth::user()->role === 'admin')
  @push('script')
  <script>
      const transactionData = @json($transactionsData);
      const productSales = @json($productSales);
      
      const barLabels = transactionData.map(item => item.date);
      const pieLabels = productSales.map(item => item.product_name);
      const barData = transactionData.map(item => item.total_transactions);
      const pieData = productSales.map(item => item.total_sold);
      
      const ctx = document.getElementById('barChart');
      const barChart = new Chart(ctx, {
          type: 'bar',
          data: {
              labels: barLabels,
              datasets: [{
                  label: 'Jumlah Penjualan',
                  data: barData,
                  backgroundColor: 'rgba(54, 162, 235, 0.2)',
                  borderColor: 'rgba(54, 162, 235, 1)',
                  borderWidth: 1
              }]
          },
          options: {
              scales: {
              y: {
                  beginAtZero: true
              }
              }
          }
      });

      const ctx2 = document.getElementById('pieChart')
      const pieChart = new Chart(ctx2, {
          type: 'pie',
          data: {
              labels: pieLabels,
              datasets: [{
                  label: 'Persentase Penjualan Produk',
                  data: pieData,
                  backgroundColor: [
                      'rgba(255, 99, 132, 0.2)',
                      'rgba(54, 162, 235, 0.2)',
                      'rgba(255, 206, 86, 0.2)',
                      'rgba(75, 192, 192, 0.2)',
                      'rgba(153, 102, 255, 0.2)',
                      'rgba(255, 159, 64, 0.2)',
                  ],
                  borderColor: [
                      'rgba(255, 99, 132, 1)',
                      'rgba(54, 162, 235, 1)',
                      'rgba(255, 206, 86, 1)',
                      'rgba(75, 192, 192, 1)',
                      'rgba(153, 102, 255, 1)',
                      'rgba(255, 159, 64, 1)',
                  ],
                  borderWidth: 1,
              }],
          },
          options: {
              resposnive: true,
              plugins: {
                  legend: {
                      position: 'top',
                  },
                  title: {
                      display: true,
                      text: 'Persentase Penjualan Produk',
                  }
              }
          }
      })
  </script>
  @endpush
@endif
