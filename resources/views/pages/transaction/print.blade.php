<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Transaksi</title>
    <style>
        body {
          font-family: Arial, sans-serif;
          font-size: 14px;
        }
        .table { 
          width: 100%; 
          border-collapse: collapse; 

        }
        .table thead{
          background-color: lightgray; 
          border-bottom: 1px solid black;
        }
        .table tr{
          border-bottom: 1px solid black;
        }
        .table tfoot{
          background-color: lightgray; 
        }
        .table th, .table td { 
          padding: 8px;
          text-align: left; 
        }
        .row{
          display: flex;
          justify-content: space-between;
        }
        .footer{
          text-align: center
        }
    </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h2 style="text-align: center;">Bukti Transaksi</h2>
    </div>
    <div class="body">
      <div class="row">
        <div class="col">
          <p><strong>Nama Pelanggan:</strong> {{ $transaction->member ? $transaction->member->name : '-' }}</p>
          <p><strong>Bergabung Sejak:</strong> {{ $transaction->member ? $transaction->member->created_at->translatedFormat('d F Y') : '-' }}</p>
          <p><strong>Poin Member:</strong> {{ $transaction->member ? $transaction->member->points : '-' }}</p>
        </div>
        <div class="col">
          <p><strong>Invoice - #{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</strong></p>
          <p><strong>Tanggal Transaksi:</strong> {{ $transaction->created_at->translatedFormat('d F Y') }}</p>
        </div>
      </div>
      <div class="row">
        <table class="table">
          <thead>
            <tr>
              <th>Nama Produk</th>
              <th>Kuantitas</th>
              <th>Harga</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($transaction->transactionDetail as $item)
              <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp. {{ number_format($item->product->price, 0, ',', '.') }}</td>
                <td>Rp. {{ number_format($item->subtotal, 0, ',', '.') }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" style="text-align: right;">Poin yang Digunakan:</th>
              <th>{{ $transaction->points_used }}</th>
            </tr>
            <tr>
              <th colspan="3" style="text-align: right;">Diskon:</th>
              <th>Rp. {{ number_format($transaction->points_used, 0, ',', '.') }}</th>
            </tr>
            <tr>
              <th colspan="3" style="text-align: right;">Total Harga yang Harus di Bayar:</th>
              <th>Rp. {{ number_format($transaction->total, 0, ',', '.') }}</th>
            </tr>
            <tr>
              <th colspan="3" style="text-align: right;">Total Bayar:</th>
              <th>Rp. {{ number_format($transaction->amount_paid, 0, ',', '.') }}</th>
            </tr>
            <tr>
              <th colspan="3" style="text-align: right;">Kembalian</th>
              <th>Rp. {{ number_format($transaction->amount_change, 0, ',', '.') }}</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    <div class="footer">
      <p><strong>Kasir:</strong> {{ $transaction->user->name }}</p>
      <p>Terima kasih telah berbelanja!</p>
    </div>
  </div>
</body>
</html>
