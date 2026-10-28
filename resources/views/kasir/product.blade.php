@extends('kasir.layout')

@section('title', 'Daftar Produk')

@section('content')
<div class="filter" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <strong>Kelola Data Produk</strong>
        <p style="margin: 4px 0 0; color: #6c757d; font-size: 13px;">Daftar barang toko dan hasil manipulasi gambar Intervention Image (Acara 29).</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary" style="text-decoration: none;">+ Tambah Produk</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th width="40">No</th>
                <th width="80">Gambar</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6; display: block;">
                        @else
                            <span style="font-size: 11px; color: #adb5bd; font-style: italic;">Tidak ada</span>
                        @endif
                    </td>
                    <td><strong>{{ $product->code }}</strong></td>
                    <td>{{ $product->name }}</td>
                    <td class="text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="text-right">
                        <span class="status {{ $product->stock > 0 ? 'paid' : 'cancelled' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty">Belum ada data produk tersimpan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
