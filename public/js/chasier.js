/* ============================================
   0. KONFIGURASI & DATA
   - Dari Laravel : window.KASIR (produk, pelanggan, route)
   - Tanpa Laravel: memakai data contoh di bawah
============================================ */
const KASIR = window.KASIR || null;

const sampleProducts = [
    { id: 1, code: "BRG001", barcode: "899123456001", name: "Indomie Goreng", price: 3500 },
    { id: 2, code: "BRG002", barcode: "899123456002", name: "Aqua 600ml", price: 4000 },
    { id: 3, code: "BRG003", barcode: "899123456003", name: "Teh Botol Sosro", price: 5000 },
    { id: 4, code: "BRG004", barcode: "899123456004", name: "Beras 5 Kg", price: 75000 },
    { id: 5, code: "BRG005", barcode: "899123456005", name: "Minyak Goreng 1 Liter", price: 18000 },
    { id: 6, code: "BRG006", barcode: "899123456006", name: "Gula Pasir 1 Kg", price: 17000 },
];

const products = KASIR ? KASIR.products : sampleProducts;

let cart = [];
let currentTransactionId = null; // terisi jika melanjutkan transaksi yang ditahan

/* ============================================
   1. FORMAT RUPIAH
============================================ */
function formatRupiah(value) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(value);
}

/* ============================================
   2. CARI BARANG / SCAN BARCODE
============================================ */
const searchInput = document.getElementById("searchProduct");
const resultBox = document.getElementById("productResults");

searchInput.addEventListener("input", searchProduct);

// Scanner barcode mengetik kode lalu menekan Enter:
// barcode/kode cocok persis, atau hasil tinggal satu -> langsung masuk keranjang
searchInput.addEventListener("keydown", function (event) {
    if (event.key !== "Enter") return;
    event.preventDefault();

    const keyword = searchInput.value.toLowerCase().trim();
    if (!keyword) return;

    const exact = products.find(
        (p) => p.barcode === keyword || p.code.toLowerCase() === keyword,
    );
    if (exact) {
        addToCart(exact.id);
        return;
    }

    const results = findProducts(keyword);
    if (results.length === 1) {
        addToCart(results[0].id);
    } else if (results.length === 0) {
        alert("Barang tidak ditemukan.");
        searchInput.select();
    }
});

document.addEventListener("click", function (event) {
    if (!event.target.closest(".search-input")) {
        resultBox.style.display = "none";
    }
});

function findProducts(rawKeyword) {
    const keyword = rawKeyword.toLowerCase().trim();
    if (!keyword) return [];

    return products.filter(
        (product) =>
            product.name.toLowerCase().includes(keyword) ||
            product.code.toLowerCase().includes(keyword) ||
            (product.barcode || "").includes(keyword),
    );
}

function hasStock(product) {
    return product.stock !== undefined && product.stock !== null;
}

function searchProduct() {
    const keyword = searchInput.value.trim();

    if (!keyword) {
        resultBox.style.display = "none";
        return;
    }

    const results = findProducts(keyword);

    if (results.length === 0) {
        resultBox.innerHTML = `
            <div class="product-item">
                Barang tidak ditemukan
            </div>
        `;
    } else {
        resultBox.innerHTML = results
            .map(
                (product) => `
                <div class="product-item" onclick="addToCart(${product.id})">
                    <div>
                        <div class="product-name">${product.name}</div>
                        <div class="product-code">${product.code} - ${product.barcode || ""}${
                            hasStock(product) ? " • Stok " + product.stock : ""
                        }</div>
                    </div>
                    <div class="product-price">${formatRupiah(product.price)}</div>
                </div>
            `,
            )
            .join("");
    }

    resultBox.style.display = "block";
}

/* ============================================
   3. KERANJANG
============================================ */
function addToCart(productId) {
    const product = products.find((p) => p.id === productId);
    if (!product) return;

    const existing = cart.find((item) => item.id === productId);
    const newQty = existing ? existing.qty + 1 : 1;

    if (hasStock(product) && newQty > product.stock) {
        alert(`Stok ${product.name} hanya ${product.stock}.`);
        return;
    }

    if (existing) {
        existing.qty = newQty;
    } else {
        cart.push({ ...product, qty: 1 });
    }

    searchInput.value = "";
    resultBox.style.display = "none";
    searchInput.focus();
    renderCart();
}

function renderCart() {
    const body = document.getElementById("cartBody");

    if (cart.length === 0) {
        body.innerHTML = `
            <tr id="emptyRow">
                <td colspan="6" class="empty-cart">
                    Keranjang masih kosong.
                    <br>
                    Silakan cari atau scan barang.
                </td>
            </tr>
        `;
        calculateTotal();
        return;
    }

    body.innerHTML = cart
        .map(
            (item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>
                    <strong>${item.name}</strong>
                    <div style="font-size:11px; color:#6c757d">${item.code}</div>
                </td>
                <td>${formatRupiah(item.price)}</td>
                <td>
                    <div class="qty-control">
                        <button onclick="changeQty(${item.id}, -1)">−</button>
                        <input type="number" value="${item.qty}" min="1"
                               onchange="updateQty(${item.id}, this.value)">
                        <button onclick="changeQty(${item.id}, 1)">+</button>
                    </div>
                </td>
                <td class="text-right">
                    <strong>${formatRupiah(item.price * item.qty)}</strong>
                </td>
                <td class="text-center">
                    <button class="remove-btn" onclick="removeItem(${item.id})">×</button>
                </td>
            </tr>
        `,
        )
        .join("");

    calculateTotal();
}

function changeQty(id, amount) {
    const item = cart.find((item) => item.id === id);
    if (!item) return;

    const newQty = item.qty + amount;

    if (newQty <= 0) {
        removeItem(id);
        return;
    }

    if (hasStock(item) && newQty > item.stock) {
        alert(`Stok ${item.name} hanya ${item.stock}.`);
        return;
    }

    item.qty = newQty;
    renderCart();
}

function updateQty(id, qty) {
    const item = cart.find((item) => item.id === id);
    if (!item) return;

    let newQty = Math.max(1, parseInt(qty) || 1);

    if (hasStock(item) && newQty > item.stock) {
        alert(`Stok ${item.name} hanya ${item.stock}.`);
        newQty = item.stock;
    }

    item.qty = newQty;
    renderCart();
}

function removeItem(id) {
    cart = cart.filter((item) => item.id !== id);
    renderCart();
}

/* ============================================
   4. PERHITUNGAN
============================================ */
function getNumber(id) {
    return parseFloat(document.getElementById(id).value) || 0;
}

function getSubtotal() {
    return cart.reduce((sum, item) => sum + item.price * item.qty, 0);
}

function getGrandTotal() {
    const subtotal = getSubtotal();
    const discount = Math.round(
        (subtotal * getNumber("discountPercent")) / 100 +
            getNumber("discountAmount"),
    );

    return Math.max(
        0,
        subtotal - discount + Math.round(getNumber("tax")) + Math.round(getNumber("otherFee")),
    );
}

function calculateTotal() {
    const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);

    document.getElementById("totalQty").innerText = totalQty;
    document.getElementById("itemCount").innerText = totalQty + " Item";
    document.getElementById("subtotal").innerText = formatRupiah(getSubtotal());
    document.getElementById("grandTotal").innerText = formatRupiah(getGrandTotal());

    renderQuickCash();
    calculateChange();
}

function calculateChange() {
    const change = getNumber("payment") - getGrandTotal();

    const changeBox = document.getElementById("changeBox");
    const changeElement = document.getElementById("change");
    const label = changeBox.querySelector(".change-label");

    if (change >= 0) {
        changeBox.classList.remove("short-payment");
        label.innerText = "KEMBALIAN";
        changeElement.innerText = formatRupiah(change);
    } else {
        changeBox.classList.add("short-payment");
        label.innerText = "UANG KURANG";
        changeElement.innerText = formatRupiah(Math.abs(change));
    }
}

/* ============================================
   5. NOMINAL CEPAT
   Contoh total Rp 37.500 -> 40.000, 50.000, 100.000, Uang Pas
============================================ */
function renderQuickCash() {
    const box = document.getElementById("quickCash");
    const total = getGrandTotal();

    if (!total) {
        box.innerHTML = "";
        return;
    }

    const suggestions = [
        ...new Set([5000, 10000, 50000, 100000].map((unit) => Math.ceil(total / unit) * unit)),
    ]
        .filter((value) => value > total)
        .sort((a, b) => a - b)
        .slice(0, 3);

    box.innerHTML =
        suggestions
            .map(
                (value) =>
                    `<button type="button" onclick="setPayment(${value})">${formatRupiah(value)}</button>`,
            )
            .join("") +
        `<button type="button" class="exact" onclick="setPayment(${total})">Uang Pas</button>`;
}

function setPayment(value) {
    document.getElementById("payment").value = value;
    calculateChange();
}

/* ============================================
   6. METODE PEMBAYARAN
============================================ */
function selectPayment(button) {
    document.querySelectorAll(".payment-method button").forEach((btn) => {
        btn.classList.remove("active");
    });
    button.classList.add("active");
}

function getPaymentMethod() {
    return document.querySelector(".payment-method button.active").dataset.method || "tunai";
}

/* ============================================
   7. KIRIM KE SERVER
============================================ */
function getPayload() {
    return {
        transaction_id: currentTransactionId,
        customer_id: document.getElementById("customerSelect").value || null,
        items: cart.map((item) => ({ id: item.id, qty: item.qty })),
        discount_percent: getNumber("discountPercent"),
        discount_amount: getNumber("discountAmount"),
        tax: getNumber("tax"),
        other_fee: getNumber("otherFee"),
        paid_amount: getNumber("payment"),
        payment_method: getPaymentMethod(),
    };
}

async function postJson(url, data) {
    const response = await fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(data),
    });

    const body = await response.json().catch(() => ({}));

    if (!response.ok) {
        const errors = body.errors ? Object.values(body.errors).flat() : [];
        throw new Error(errors[0] || body.message || "Terjadi kesalahan pada server.");
    }

    return body;
}

/* ============================================
   8. TAHAN, BAYAR, BATAL
============================================ */
function resetTransaction() {
    cart = [];
    currentTransactionId = null;
    document.getElementById("payment").value = "";
    document.getElementById("discountPercent").value = 0;
    document.getElementById("discountAmount").value = 0;
    document.getElementById("tax").value = 0;
    document.getElementById("otherFee").value = 0;
    document.getElementById("customerSelect").value = "";
    document.getElementById("memberInput").value = "";
    renderCart();
}

async function holdTransaction() {
    if (cart.length === 0) {
        alert("Tidak ada transaksi untuk ditahan.");
        return;
    }

    if (!KASIR) {
        alert("Transaksi berhasil ditahan.");
        return;
    }

    try {
        const result = await postJson(KASIR.routes.hold, getPayload());

        alert(result.message);
        document.getElementById("heldCount").innerText = result.held_count;
        document.getElementById("transactionNumber").innerText = result.next_number;
        resetTransaction();
        searchInput.focus();
    } catch (error) {
        alert(error.message);
    }
}

async function processPayment() {
    if (cart.length === 0) {
        alert("Keranjang masih kosong.");
        return;
    }

    const total = getGrandTotal();
    const payment = getNumber("payment");

    if (payment < total) {
        alert("Uang pembayaran masih kurang.");
        return;
    }

    if (!KASIR) {
        alert(
            "Pembayaran berhasil!\n\n" +
                "Total   : " + formatRupiah(total) + "\n" +
                "Bayar   : " + formatRupiah(payment) + "\n" +
                "Kembali : " + formatRupiah(payment - total),
        );
        resetTransaction();
        return;
    }

    const button = document.querySelector(".btn-pay");
    button.disabled = true;

    try {
        const result = await postJson(KASIR.routes.store, getPayload());
        window.location.href = result.redirect; // halaman struk, otomatis print
    } catch (error) {
        alert(error.message);
        button.disabled = false;
    }
}

function cancelTransaction() {
    if (cart.length === 0 && currentTransactionId === null) {
        return;
    }

    if (!confirm("Batalkan transaksi ini?")) {
        return;
    }

    // Transaksi yang ditahan tidak dihapus di sini, tetap ada di daftar Ditahan
    if (currentTransactionId !== null && KASIR) {
        window.location.href = KASIR.routes.index;
        return;
    }

    resetTransaction();
}

/* ============================================
   9. PELANGGAN / MEMBER
============================================ */
const customerSelect = document.getElementById("customerSelect");
const memberInput = document.getElementById("memberInput");

// Ketik no. member / HP -> pelanggan terpilih otomatis
memberInput.addEventListener("change", function () {
    const key = memberInput.value.trim().toLowerCase();
    if (!key || !KASIR) return;

    const customer = KASIR.customers.find(
        (c) =>
            (c.phone || "").toLowerCase() === key ||
            (c.member_no || "").toLowerCase() === key,
    );

    if (customer) {
        customerSelect.value = customer.id;
    } else {
        alert("Member tidak ditemukan.");
    }
});

customerSelect.addEventListener("change", function () {
    if (!KASIR) return;

    const customer = KASIR.customers.find((c) => String(c.id) === customerSelect.value);
    memberInput.value = customer ? customer.member_no || customer.phone || "" : "";
});

/* ============================================
   10. TANGGAL & NOMOR TRANSAKSI
============================================ */
function updateDate() {
    document.getElementById("currentDate").innerText =
        new Date().toLocaleString("id-ID");
}

// Tanpa Laravel, nomor dibuat di browser. Dengan Laravel, nomor datang dari server.
if (!KASIR) {
    const now = new Date();
    const y = now.getFullYear();
    const m = String(now.getMonth() + 1).padStart(2, "0");
    const d = String(now.getDate()).padStart(2, "0");
    document.getElementById("transactionNumber").innerText = `TRX-${y}${m}${d}-001`;
}

updateDate();
setInterval(updateDate, 1000);

/* ============================================
   11. SHORTCUT KEYBOARD
============================================ */
document.addEventListener("keydown", function (event) {
    // F2 = fokus pencarian / scan barcode
    if (event.key === "F2") {
        event.preventDefault();
        searchInput.focus();
    }

    // F4 = fokus uang dibayar
    if (event.key === "F4") {
        event.preventDefault();
        document.getElementById("payment").focus();
    }

    // ESC = batal transaksi
    if (event.key === "Escape") {
        cancelTransaction();
    }
});

/* ============================================
   12. INISIALISASI (termasuk melanjutkan transaksi ditahan)
============================================ */
if (KASIR && KASIR.resume) {
    const resume = KASIR.resume;

    currentTransactionId = resume.id;

    resume.items.forEach((row) => {
        const product = products.find((p) => p.id === row.id);
        if (product) cart.push({ ...product, qty: row.qty });
    });

    document.getElementById("discountAmount").value = resume.discount_amount || 0;
    document.getElementById("tax").value = resume.tax || 0;
    document.getElementById("otherFee").value = resume.other_fee || 0;

    if (resume.customer_id) {
        customerSelect.value = resume.customer_id;
        customerSelect.dispatchEvent(new Event("change"));
    }
}

renderCart();
