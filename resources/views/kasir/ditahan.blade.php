@extends('kasir.layout')

@section('title', 'Transaksi Ditahan')

@section('content')
<div class="card">
    <table>
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Waktu</th>
                <th class="text-right">Item</th>
                <th class="text-right">Total</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td><strong>{{ $transaction->transaction_number }}</strong></td>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-right">{{ $transaction->total_qty }} Item</td>
                    <td class="text-right">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
                    <td class="text-right actions">
                        <a class="btn btn-primary" href="{{ route('kasir.index', ['lanjut' => $transaction->id]) }}">Lanjutkan</a>
                        <form method="POST" action="{{ route('kasir.held.destroy', $transaction) }}"
                              onsubmit="return confirm('Hapus transaksi yang ditahan ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty">Tidak ada transaksi yang ditahan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
