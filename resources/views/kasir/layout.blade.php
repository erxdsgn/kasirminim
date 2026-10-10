<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Kasir</title>
    <link rel="stylesheet" href="{{ asset('css/kasir-pages.css') }}">
</head>
<body>

<header class="page-header">
    <div class="brand">TOKO RETAIL MAKMUR</div>
    <nav>
        <a href="{{ route('kasir.index') }}">Transaksi Baru</a>
        <a href="{{ route('kasir.held') }}" @class(['active' => request()->routeIs('kasir.held')])>Ditahan</a>
        <a href="{{ route('kasir.history') }}" @class(['active' => request()->routeIs('kasir.history')])>Riwayat</a>
        <a href="{{ route('kasir.closing') }}" @class(['active' => request()->routeIs('kasir.closing')])>Tutup Kasir</a>
    </nav>
</header>

<main class="page">
    <h1>@yield('title')</h1>

    @if (session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    @yield('content')
</main>

</body>
</html>
