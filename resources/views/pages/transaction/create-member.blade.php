
@extends('layouts.main')

@section('title', 'Penjualan')

@section('header')
  @include('components.header')
@endsection

@section('aside')
  @include('components.aside')
@endsection

@section('title', 'Penjualan')

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
  <div class="container-fluid d-flex flex-column" style="min-height: 500px">
    <div class="row">
      <div class="col-12 max-h-auto">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-lg-6 col-md-12 pe-5">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col" class="pt-0">Nama Produk</th>
                      <th scope="col" class="pt-0">Harga</th>
                      <th scope="col" class="pt-0">Kuantitas</th>
                      <th scope="col" class="pt-0">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody class="table-group-divider">
                    @foreach ($items as $item)
                      <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot class="table-group-divider">
                    <td colspan="3" class="text-end"><strong>Kembalian:</strong></td>
                    <td id="totalChange">Rp {{ number_format($amountChange, 0, ',', '.') }}</td>
                  </tfoot>
                  <tfoot class="table-group-divider">
                    <td colspan="3" class="text-end"><strong>Total Harga:</strong></td>
                    <td>
                      <div id="totalPriceBeforeDiscount">Rp {{ number_format($total, 0, ',', '.') }}</div>
                      <div class="d-none" id="totalPriceAfterDiscount">Rp {{ number_format($total, 0, ',', '.') }}</div>
                    </td>
                  </tfoot>
                  <tfoot class="table-group-divider">
                    <td colspan="3" class="text-end"><strong>Total Bayar:</strong></td>
                    <td id="totalPaid">Rp {{ number_format($amountPaid, 0, ',', '.') }}</td>
                  </tfoot>
                </table>
              </div>
              <div class="col-lg-6 col-md-12 ps-5">
                <form action="{{ route('transaction.storeMember') }}" method="POST">
                  @csrf
                  <div class="row">
                    <div class="form-group">
                      <label for="name" class="col-md-12">Nama Member (identitas)</label>
                      <div class="col-md-12">
                        <input type="text" class="form-control form-control-line @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $memberName) }}">
                        @error('name')
                          <div class="invalid-feedback">
                            Nama member harus diisi!
                          </div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group">
                      <label for="pointsEarned" class="col-md-12">Poin</label>
                      <div class="col-md-12">
                        <input type="number" class="form-control form-control-line" id="pointsEarned" value="{{ $points }}" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-check ms-4">
                      <div class="col-md-12">
                        <input type="checkbox" class="form-check-input" id="check" name="check_poin" onchange="updateTotal()" {{ $isNewMember ? 'disabled' : '' }}>
                        <label for="check" class="form-check-label">Gunakan poin</label>
                        @if ($isNewMember)
                          <small class="text-danger">Poin tidak dapat digunakan pada pembelanjaan pertama</small>
                        @endif
                      </div>
                    </div>
                  </div>
                  <input type="hidden" name="items" value="{{ old('items', json_encode($items)) }}">
                  <input type="hidden" name="total" value="{{ $total }}" id="total">
                  <input type="hidden" name="points_used" id="points_used">
                  <input type="hidden" name="points_earned" value="{{ $pointsEarned }}">
                  <input type="hidden" name="amount_paid" value="{{ $amountPaid }}">
                  <input type="hidden" name="amount_change" value="{{ $amountChange }}">
                  <input type="hidden" name="phone_number" value="{{ $phoneNumber }}">
                  <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                  <div class="row">
                    <div class="col-md-12 text-end">
                      <button class="btn btn-primary" type="submit">Pesan</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('script')
  <script>
    const points = parseInt(document.getElementById('pointsEarned').value);
    const totalInput = document.querySelector('input[name="total"]');
    const amountPaid = parseInt(document.querySelector('input[name="amount_paid"]').value);
    const amountChangeInput = document.querySelector('input[name="amount_change"]');
    const amountChangeElement = document.getElementById('totalChange');
    const checkbox = document.getElementById('check');
    const pointsUsedInput = document.getElementById('points_used')
    const totalPricBeforeDiscounteElement = document.getElementById('totalPriceBeforeDiscount');
    const totalPriceAfterDiscountElement = document.getElementById('totalPriceAfterDiscount');

    function updateTotal() {
      let originalTotal = totalInput.dataset.originalTotal || (totalInput.dataset.originalTotal = totalInput.value);
      originalTotal = parseInt(originalTotal);

      let newTotal = checkbox.checked ? Math.max(originalTotal - points, 0) : originalTotal;
      let newAmountChange = Math.max(amountPaid - newTotal, 0);

      pointsUsedInput.value = checkbox.checked ? points : 0;

      totalInput.value = newTotal;
      amountChangeInput.value = newAmountChange;
      amountChangeElement.innerText = `Rp ${newAmountChange.toLocaleString('id-ID')}`;

      totalPricBeforeDiscounteElement.classList.toggle('text-decoration-line-through', checkbox.checked);
      totalPriceAfterDiscountElement.classList.toggle('d-none', !checkbox.checked);
      totalPriceAfterDiscountElement.innerText = `Rp. ${newTotal.toLocaleString('id-ID')}`;
    }
  </script>
@endpush
    
  