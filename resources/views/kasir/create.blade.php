@extends('kasir.layout')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="card" style="max-width: 550px; padding: 24px; margin: 0 auto;">

    @if ($errors->any())
        <div class="alert" style="background: #f8d7da; color: #842029; margin-bottom: 20px; border-radius: 6px; padding: 12px;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Kode Produk (*)</label>
            <input type="text" name="code" value="{{ old('code') }}" required placeholder="Contoh: PRD001"
                   style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 6px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nama Produk (*)</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama barang"
                   style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 6px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Harga (Rp) (*)</label>
            <input type="number" name="price" value="{{ old('price') }}" required placeholder="0"
                   style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 6px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Stok Awal (*)</label>
            <input type="number" name="stock" value="{{ old('stock', 0) }}" required placeholder="0"
                   style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 6px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Gambar Produk (Acara 29)</label>
            <input type="file" name="image" accept="image/*"
                   style="width: 100%; padding: 8px; border: 1px dashed #6c757d; border-radius: 6px; background: #f8f9fa; box-sizing: border-box;">
            <small style="color: #6c757d; display: block; margin-top: 6px; font-size: 12px;">Format: JPG, PNG, WEBP (Maksimal 2MB). Gambar akan di-resize & di-crop otomatis ke ukuran 800x800px.</small>
        </div>

        <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Simpan Produk</button>
            <a href="{{ route('products.index') }}" class="btn btn-light" style="text-decoration: none; text-align: center; width: 100px;">Batal</a>
        </div>
    </form>
</div>
@endsection
