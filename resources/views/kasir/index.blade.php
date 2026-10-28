<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir Retail POS</title>
    <link rel="stylesheet" href="{{ asset('css/chasier.css') }}">
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
            <!-- Link Akses Kelola Produk (Acara 29) -->
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
        },
    };
</script>
<script src="{{ asset('js/chasier.js') }}"></script>
</body>
</html>
