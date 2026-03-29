<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan TiketApp</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
        .total { text-align: right; margin-top: 20px; font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>
<div class="header">
    <h2>LAPORAN PENJUALAN TIKET</h2>
    <p>Dicetak pada: {{ date('d F Y H:i') }}</p>
</div>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>ID Transaksi</th>
        <th>Nama Pembeli</th>
        <th>Nama Event</th>
        <th>Kategori Tiket</th>
        <th>Tanggal Bayar</th>
        <th>Harga</th>
    </tr>
    </thead>
    <tbody>
    @foreach($transactions as $index => $trx)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>#TRX-{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td>{{ $trx->user->name }}</td>
            <td>{{ $trx->ticketCategory->event->title }}</td>
            <td>{{ $trx->ticketCategory->name }}</td>
            <td>{{ $trx->updated_at->format('d/m/Y') }}</td>
            <td>Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="total">
    Total Pendapatan: Rp {{ number_format($totalRevenue, 0, ',', '.') }}
</div>
</body>
</html>
