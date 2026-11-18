<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir Retail POS</title>
    <link rel="stylesheet" href="{{ asset('css/chasier.css') }}">

    <!-- MIDTRANS SNAP SDK (ACARA 37) -->
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.clientKey') }}"></script>
</head>
<body>

<div class="pos-container">

    <!-- HEADER -->
    <header class="header">
        <div>
            <div class="store-name">TOKO RETAIL MAKMUR</div>
            <div class="store-info">
                Jl. Contoh No. 123 • Jember • Telp. 0812-xxxx-xxxx
            </div>
        </div>

        <!-- MENU KASIR -->
        <nav class="header-nav">
            <a href="{{ route('kasir.index') }}" class="active">Transaksi Baru</a>
            <a href="{{ route('kasir.held') }}">Ditahan <span class="badge" id="heldCount">{{ $heldCount ?? 0 }}</span></a>
            <a href="{{ route('kasir.history') }}">Riwayat</a>
            <a href="{{ route('kasir.closing') }}">Tutup Kasir</a>
            <a href="{{ route('products.index') }}" target="_blank">Kelola Produk</a>
        </nav>

        <div class="transaction-info">
            <div>No. Transaksi</div>
            <div class="transaction-number" id="transactionNumber">{{ $transactionNumber ?? '-' }}</div>
            <div id="currentDate"></div>
        </div>
    </header>

    <!-- MAIN -->
    <main class="main">

        <!-- ================= LEFT ================= -->
        <section>

            <!-- SEARCH -->
            <div class="card">
                <div class="card-header">Tambah Barang</div>
                <div class="card-body">

                    <div class="search-area">
                        <div class="search-input">
                            <input type="text" id="searchProduct"
                                   placeholder="Cari nama barang / kode / scan barcode..."
                                   autocomplete="off" autofocus>
                            <div class="product-results" id="productResults"></div>
                        </div>

                        <button type="button" class="btn btn-primary" onclick="searchProduct()">Cari</button>
                    </div>

                    <!-- CUSTOMER -->
                    <div class="customer-area">
                        <div class="form-group">
                            <label for="customerSelect">Pelanggan</label>
                            <select class="form-control" id="customerSelect">
                                <option value="">Umum</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->name }} ({{ ($customer->type ?? '') === 'vip' ? 'Member VIP' : 'Member' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="memberInput">No. Member / HP</label>
                            <input type="text" class="form-control" id="memberInput" placeholder="Opsional">
                        </div>
                    </div>

                </div>
            </div>

            <!-- CART -->
            <div class="card cart-card">
                <div class="card-header">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span>Keranjang Belanja</span>
                        <span id="itemCount">0 Item</span>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th width="40">No</th>
                                <th>Barang</th>
                                <th width="100">Harga</th>
                                <th width="120" class="text-center">Qty</th>
                                <th width="120" class="text-right">Subtotal</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                            <tr id="emptyRow">
                                <td colspan="6" class="empty-cart">
                                    Keranjang masih kosong.
                                    <br>
                                    Silakan cari atau scan barang.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>

        <!-- ================= RIGHT ================= -->
        <aside>
            <div class="card">
                <div class="card-header">Ringkasan Pembayaran</div>
                <div class="card-body">
                    <div class="payment-summary">

                        <div class="summary-row">
                            <span>Total Item</span>
                            <strong id="totalQty">0</strong>
                        </div>

                        <div class="summary-row">
                            <span>Subtotal</span>
                            <strong id="subtotal">Rp 0</strong>
                        </div>

                        <div class="summary-row">
                            <span>Diskon (%)</span>
                            <input type="number" id="discountPercent" value="0" min="0" max="100" step="any"
                                   oninput="calculateTotal()">
                        </div>

                        <div class="summary-row">
                            <span>Diskon (Rp)</span>
                            <input type="number" id="discountAmount" value="0" min="0" step="any"
                                   oninput="calculateTotal()">
                        </div>

                        <div class="summary-row">
                            <span>Pajak / PPN</span>
                            <input type="number" id="tax" value="0" min="0" step="any"
                                   oninput="calculateTotal()">
                        </div>

                        <div class="summary-row">
                            <span>Biaya Lain</span>
                            <input type="number" id="otherFee" value="0" min="0" step="any"
                                   oninput="calculateTotal()">
                        </div>

                        <div class="total-box">
                            <div class="total-label">TOTAL AKHIR</div>
                            <div class="total-value" id="grandTotal">Rp 0</div>
                        </div>

                        <!-- PAYMENT -->
                        <div class="payment-box">
                            <div class="payment-label">Uang Dibayar</div>
                            <input type="number" id="payment" class="payment-input"
                                   placeholder="0" min="0" step="any" oninput="calculateChange()">

                            <!-- NOMINAL CEPAT -->
                            <div class="quick-cash" id="quickCash"></div>

                            <div class="payment-label" style="margin-top:10px">Metode Pembayaran</div>

                            <div class="payment-method">
                                <button type="button" class="active" data-method="tunai" onclick="selectPayment(this)">Tunai</button>
                                <button type="button" data-method="qris" onclick="selectPayment(this)">QRIS</button>
                                <button type="button" data-method="debit" onclick="selectPayment(this)">Debit</button>
                                <button type="button" data-method="kredit" onclick="selectPayment(this)">Kredit</button>
                                <button type="button" data-method="ewallet" onclick="selectPayment(this)">E-Wallet</button>
                                <button type="button" data-method="transfer" onclick="selectPayment(this)">Transfer</button>
                            </div>

                            <!-- CHANGE -->
                            <div class="change-box" id="changeBox">
                                <div class="change-label">KEMBALIAN</div>
                                <div class="change-value" id="change">Rp 0</div>
                            </div>
                        </div>

                        <!-- ACTION -->
                        <div class="action-area">
                            <button type="button" class="btn btn-warning" onclick="holdTransaction()">Tahan</button>
                            <button type="button" class="btn btn-danger" onclick="cancelTransaction()">Batal</button>
                            <button type="button" class="btn btn-success btn-pay" onclick="processPayment()">BAYAR &amp; CETAK</button>
                        </div>

                    </div>
                </div>
            </div>
        </aside>

    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <span>Kasir: {{ $cashierName ?? auth()->user()->name ?? 'Kasir' }}</span>
        <span>F2 Cari Barang • F4 Bayar • ESC Batal</span>
    </footer>

</div>

<script>
    window.KASIR = {
        products: JSON.parse('{!! addslashes(json_encode($products ?? [])) !!}'),
        customers: JSON.parse('{!! addslashes(json_encode($customers ?? [])) !!}'),
        resume: JSON.parse('{!! addslashes(json_encode($resume ?? null)) !!}'),
        routes: {
            index: "{{ route('kasir.index') }}",
            store: "{{ route('kasir.store') }}",
            hold: "{{ route('kasir.hold') }}",
            printThermal: "{{ route('kasir.print.thermal') }}",
            checkout: "{{ route('checkout') }}" // ROUTE ACARA 37
        },
    };
</script>
<script src="{{ asset('js/chasier.js') }}"></script>

<script>
// 1. FUNGSI MEMILIH METODE PEMBAYARAN (MENGAKTIFKAN TOMBOL KLIK)
function selectPayment(element) {
    document.querySelectorAll('.payment-method button').forEach(btn => {
        btn.classList.remove('active');
    });
    element.classList.add('active');
}

// 2. FUNGSI PROSES PEMBAYARAN (TUNAI VS MIDTRANS SNAP)
async function processPayment() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const discountAmount = parseFloat(document.getElementById('discountAmount')?.value) || 0;

    // Deteksi tombol pembayaran yang aktif
    const activeMethodBtn = document.querySelector('.payment-method button.active');
    const paymentMethod = activeMethodBtn ? activeMethodBtn.getAttribute('data-method') : 'tunai';

    // Ambil item belanja dari keranjang
    let items = [];
    if (typeof cart !== 'undefined' && Array.isArray(cart) && cart.length > 0) {
        items = cart.map(item => ({
            name: item.name || item.nama,
            qty: item.qty || item.quantity || 1,
            price: item.price || item.harga || 0
        }));
    } else {
        const rows = document.querySelectorAll('#cartBody tr:not(#emptyRow)');
        rows.forEach(row => {
            const name = row.cells[1]?.innerText?.trim();
            const priceText = row.cells[2]?.innerText?.replace(/[^0-9]/g, '');
            const qtyInput = row.querySelector('input[type="number"]');
            const qty = qtyInput ? parseInt(qtyInput.value) : 1;

            if (name && priceText) {
                items.push({
                    name: name,
                    qty: qty,
                    price: parseFloat(priceText) || 0
                });
            }
        });
    }

    if (items.length === 0) {
        alert('Keranjang belanja masih kosong!');
        return;
    }

    // Hitung total belanja
    let totalBelanja = items.reduce((sum, i) => sum + (i.price * i.qty), 0) - discountAmount;
    if (totalBelanja <= 0) totalBelanja = 1000;

    // PEMBAYARAN MIDTRANS SNAP (QRIS / E-WALLET / DEBIT / KREDIT / TRANSFER)
    if (['qris', 'ewallet', 'debit', 'kredit', 'transfer'].includes(paymentMethod)) {
        try {
            const response = await fetch(window.KASIR.routes.checkout, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    gross_amount: totalBelanja,
                    customer_name: "Pelanggan POS"
                })
            });

            const result = await response.json();

            if (result.snap_token) {
                // Munculkan Pop-up Midtrans Snap
                window.snap.pay(result.snap_token, {
                    onSuccess: function(res) {
                        alert('Pembayaran Midtrans Berhasil!');
                        window.location.reload();
                    },
                    onPending: function(res) {
                        alert('Menunggu Pembayaran Pelanggan...');
                    },
                    onError: function(res) {
                        alert('Pembayaran Midtrans Gagal!');
                    },
                    onClose: function() {
                        alert('Jendela Pembayaran Midtrans Ditutup.');
                    }
                });
            } else {
                alert('Gagal mendapatkan token Midtrans: ' + (result.message || 'Error Server'));
            }
        } catch (error) {
            console.error('Error Midtrans:', error);
            alert('Terjadi kesalahan saat menghubungkan ke Midtrans.');
        }
        return;
    }

    // PEMBAYARAN TUNAI (CETAK STRUK THERMAL)
    try {
        const response = await fetch(window.KASIR.routes.printThermal, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                items: items,
                diskon: discountAmount
            })
        });

        const result = await response.json();

        if (result.status === 'success') {
            alert('Pembayaran Berhasil!\n' + result.message);
            window.location.reload();
        } else {
            alert('Gagal mencetak struk: ' + (result.message || result.error));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses pembayaran tunai.');
    }
}
</script>
</body>
</html>
