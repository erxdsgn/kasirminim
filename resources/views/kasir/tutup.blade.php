@extends('kasir.layout')

@section('title', 'Tutup Kasir')

@section('content')
<form method="GET" class="filter">
    <label for="saldo_awal">Saldo awal (Rp)</label>
    <input type="number" id="saldo_awal" name="saldo_awal" min="0" value="{{ $openingBalance }}">
    <button class="btn btn-primary">Hitung</button>
</form>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Metode Pembayaran</th>
                <th class="text-right">Jumlah Transaksi</th>
                <th class="text-right">Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($methods as $key => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-right">{{ $byMethod[$key]->total_transactions ?? 0 }}</td>
                    <td class="text-right">Rp {{ number_format($byMethod[$key]->total ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Total hari ini</th>
                <th class="text-right">{{ $totalCount }}</th>
                <th class="text-right">Rp {{ number_format($totalSales, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="card closing-box">
    <div class="row"><span>Saldo awal</span><strong>Rp {{ number_format($openingBalance, 0, ',', '.') }}</strong></div>
    <div class="row"><span>Penjualan tunai</span><strong>Rp {{ number_format($cashSales, 0, ',', '.') }}</strong></div>
    <div class="row total"><span>Saldo akhir (uang di laci)</span><strong>Rp {{ number_format($closingBalance, 0, ',', '.') }}</strong></div>
    <div class="row muted"><span>Transaksi dibatalkan hari ini</span><span>{{ $cancelledCount }}</span></div>
</div>

<button class="btn btn-light" onclick="window.print()">Cetak Laporan</button>
@endsection
