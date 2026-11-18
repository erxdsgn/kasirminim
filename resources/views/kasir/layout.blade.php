<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Kasir</title>
    <link rel="stylesheet" href="{{ asset('css/kasir-pages.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>@yield('title') - Kasir</title>

        <link rel="stylesheet" href="{{ asset('css/kasir-pages.css') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            console.log('Layout Blade berhasil dimuat');
        </script>
    </head>

    <main class="page">
        <h1>@yield('title')</h1>

        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.Echo) {
                console.error('Laravel Echo belum berhasil dimuat!');
                return;
            }
            console.log('Laravel Echo berhasil dimuat!');
            window.Echo.private('user.{{ auth()->id() }}')
                .listen('.OrderPlaced', (e) => {
                    console.log('Order Placed:', e);
                    alert(
                        `Pesanan baru #${e.order_id} berhasil dibuat oleh ${e.user_name} dengan total Rp ${e.total}`
                    );
                });
        });
    </script>

</body>

</html>
