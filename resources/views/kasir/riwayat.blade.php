@extends('kasir.layout')

@section('title', 'Riwayat Transaksi')

@section('content')
<form method="GET" class="filter">
    <label for="tanggal">Tanggal</label>
    <input type="date" id="tanggal" name="tanggal" value="{{ $date }}">
    <button class="btn btn-primary">Tampilkan</button>
</form>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Waktu</th>
                <th>Kasir</th>
                <th class="text-right">Item</th>
                <th class="text-right">Total</th>
                <th>Metode</th>
                <th>Status</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td><strong>{{ $transaction->transaction_number }}</strong></td>
                    <td>{{ $transaction->created_at->format('H:i') }}</td>
                    <td>{{ $transaction->user->name ?? 'Admin' }}</td>
                    <td class="text-right">{{ $transaction->total_qty }}</td>
                    <td class="text-right">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($transaction->payment_method) }}</td>
                    <td>
                        <span class="status {{ $transaction->status }}">
                            {{ $transaction->status === 'paid' ? 'Lunas' : 'Dibatalkan' }}
                        </span>
                    </td>
                    <td class="text-right actions">
                        <a class="btn btn-light" href="{{ route('kasir.receipt', $transaction) }}">Struk</a>
                        @if ($transaction->status === 'paid')
                            <form method="POST" action="{{ route('kasir.cancel', $transaction) }}"
                                  onsubmit="return confirm('Batalkan transaksi {{ $transaction->transaction_number }}? Stok akan dikembalikan.')">
                                @csrf
                                <button class="btn btn-danger">Batalkan</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty">Belum ada transaksi pada tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
