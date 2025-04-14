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
    <div class="container-fluid d-flex flex-column" style="min-height: 500px">
      <div class="row">
        <div class="col-12 max-h-auto">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-5 p-0">
                  <form action="{{ route('transaction.store') }}" method="POST">
                    @csrf
                    <table class="table table-sm" id="cartTable">
                      <thead>
                        <tr>
                          <th scope="col"></th>
                          <th scope="col">Nama</th>
                          <th scope="col">Harga</th>
                          <th scope="col">Kuantitas</th>
                          <th scope="col">Subtotal</th>
                        </tr>
                      </thead>
                      <tbody class="table-group-divider">
                      </tbody>
                      <tfoot class="table-group-divider">
                        <th scope="col" colspan="4" class="text-center">Total</th>
                        <td id="totalAmount">Rp 0</td>
                      </tfoot>
                    </table>
                    <input type="hidden" name="items" id="items">
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                    <input type="hidden" name="total" id="total" value="0">
                    <div class="row">
                      <div class="form-group">
                        <label for="member" class="col-md-12">Member Status <small class="text-danger">Dapat juga membuat member</small></label>
                        <div class="col-md-12">
                          <select class="form-select shadow-none form-control-line" name="status" id="status">
                            <option selected disabled hidden>Pilih Status Pelanggan</option>
                            <option value="member">Member</option>
                            <option value="non-member">Non-Member</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row" id="phoneNumberContainer" style="display: none;">
                      <div class="form-group">
                        <label for="phone_number" class="col-md-12">Nomor Telepon <small class="text-danger">(daftar/gunakan member)</small></label>
                        <div class="col-md-12">
                          <input type="text" class="form-control form-control-line" id="phone_number" name="phone_number">
                          <div class="invalid-feedback">
                            Nomor telepon harus diisi!
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group">
                        <label for="total_pay" class="col-md-12">Total Bayar</label>
                        <div class="col-md-12">
                          <input type="text" name="amount_paid" class="form-control form-control-line" id="total_pay" placeholder="Rp. 0">
                          <small id="error-message" class="text-danger d-none">Jumlah bayar kurang</small>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 text-end">
                        <button class="btn btn-primary" type="submit">Pesan</button>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="col-md-7 overflow-auto" style="max-height: 500px">
                  <div class="row g-3">
                    @foreach ($products as $product)
                      <div class="col-md-4 col-sm-6">
                        <div class="card">
                          <img src="{{ asset('storage/' . $product['image']) }}" class="card-img-top" alt="{{ $product['name'] }}">
                          <div class="card-body">
                            <h5 class="card-title">{{ $product['name'] }}</h5>
                            <p class="card-text fw-bold">Stok {{ $product['stock'] }}</p>
                            <p class="card-text">Rp. {{ number_format($product['price'], 0, ',', '.') }}</p>
                            <button class="btn btn-primary add-to-cart" data-id="{{ $product['id'] }}" data-name="{{ $product['name'] }}" data-price="{{ $product['price'] }}" data-stock="{{ $product['stock'] }}">
                              Tambah
                            </button>
                          </div>
                        </div>
                      </div>
                    @endforeach
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

@push('script')
  <script>
    const totalPayInput = document.querySelector("#total_pay");
    const totalAmount = document.querySelector("#totalAmount");
    const errorMessage = document.getElementById("error-message");
    const submitButton = document.querySelector("button[type='submit']");
    const cartTableBody = document.querySelector("#cartTable tbody");
    const itemsInput = document.querySelector("#items");
    const statusSelect = document.querySelector("#status");
    const phoneNumberContainer = document.querySelector("#phoneNumberContainer");

    function formatRupiah(value) {
      return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
      }).format(value).replace("Rp", "Rp.").trim();
    }

    function checkTotalPay() {
      const totalPayValue = parseInt(totalPayInput.value.replace(/[^0-9]/g, ''), 10) || 0;
      const totalValue = parseInt(totalAmount.textContent.replace(/[^0-9]/g, ''), 10) || 0;

      if (totalPayValue < totalValue) {
        errorMessage.classList.remove('d-none');
        submitButton.disabled = true;
      } else {
        errorMessage.classList.add('d-none');
        submitButton.disabled = false;
      }
    }

    document.addEventListener("DOMContentLoaded", function () {
      let cart = [];

      function updateCartTable() {
        cartTableBody.innerHTML = "";
        let total = 0;

        cart.forEach((item, index) => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td><button class="btn btn-danger btn-sm remove-item" data-index="${index}">X</button></td>
            <td>${item.name}</td>
            <td>Rp ${item.price.toLocaleString()}</td>
            <td><input type="number" class="form-control form-control-sm update-qty" data-index="${index}" value="${item.quantity}" min="1" max="${item.stock}"></td>
            <td>Rp ${(item.price * item.quantity).toLocaleString()}</td>
          `;
          cartTableBody.appendChild(row);
          total += item.price * item.quantity;
        });
        
        totalAmount.textContent = `Rp. ${total.toLocaleString()}`;
        itemsInput.value = JSON.stringify(cart);
        document.querySelector("#total").value = total;

        checkTotalPay();
      }

      totalPayInput.addEventListener("input", function () {
        let rawValue = this.value.replace(/[^0-9]/g, "");
        this.dataset.rawValue = rawValue;
        this.value = formatRupiah(rawValue);
        checkTotalPay(); 
      });

      document.querySelectorAll(".add-to-cart").forEach(button => {
        button.addEventListener("click", function () {
          const id = this.getAttribute("data-id");
          const name = this.getAttribute("data-name");
          const price = parseInt(this.getAttribute("data-price"));
          const stock = parseInt(this.getAttribute("data-stock"));
          
          let existingItem = cart.find(item => item.id === id);
          
          if (existingItem) {
            if (existingItem.quantity < stock) {
              existingItem.quantity += 1;
            } else {
              alert("Stok tidak mencukupi");
            }
          } else {
            cart.push({ id, name, price, quantity: 1, stock });
          }
          updateCartTable();
        });
      });

      cartTableBody.addEventListener("click", function (event) {
        if (event.target.classList.contains("remove-item")) {
          const index = event.target.getAttribute("data-index");
          cart.splice(index, 1);
          updateCartTable();
        }
      });

      cartTableBody.addEventListener("input", function (event) {
        if (event.target.classList.contains("update-qty")) {
          const index = event.target.getAttribute("data-index");
          const newQty = parseInt(event.target.value);
          if (newQty > 0 && newQty <= cart[index].stock) {
            cart[index].quantity = newQty;
          } else {
            event.target.value = cart[index].quantity;
            alert("Jumlah melebihi stok tersedia");
          }
          updateCartTable();
        }
      });

      statusSelect.addEventListener("change", function () {
        if (statusSelect.value === "member") {
          phoneNumberContainer.style.display = "block";
        } else {
          phoneNumberContainer.style.display = "none";
        }
      });

      totalPayInput.addEventListener("input", function () {
        let rawValue = this.value.replace(/[^0-9]/g, "");
        this.dataset.rawValue = rawValue;
        this.value = formatRupiah(rawValue);
      });

      document.querySelector("form").addEventListener("submit", function (e) {
        const phoneNumberInputField = document.getElementById('phone_number');
        
        if (cart.length === 0) {
          e.preventDefault();
          showBasicAlert('', 'Silahkan pilih produk yang ingin dibeli!', 'warning');
          return;
        }   
        
        if (statusSelect.value === "member" && phoneNumberInputField.value.trim() === "") {
          e.preventDefault();
          phoneNumberInputField.classList.add('is-invalid');
          return;
        } else {
          phoneNumberInputField.classList.remove('is-invalid');
        }

        totalPayInput.value = totalPayInput.dataset.rawValue || "0";
      });
    });
  </script>
@endpush