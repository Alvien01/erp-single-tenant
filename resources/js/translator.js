/**
 * ERP Auto Translator — ID ↔ EN
 * Uses a dictionary + TreeWalker to translate visible text nodes.
 * Persists language choice in localStorage.
 */
window.ERPTranslator = (function () {
    // ── Dictionary (EN → ID) ────────────────────────────────────
    const dict = {
        // Topbar / Navbar
        'Search across ERP...': 'Cari di seluruh ERP...',
        'Profile Settings': 'Pengaturan Profil',
        'Sign out': 'Keluar',

        // Sidebar – Sections
        'Dashboard': 'Dasbor',
        'Content': 'Konten',
        'Config Basic': 'Konfigurasi Dasar',
        'Website & Marketing': 'Website & Pemasaran',
        'Discuss / Chat': 'Diskusi / Obrolan',
        'Documents & Sign': 'Dokumen & Tanda Tangan',
        'Master Data': 'Data Master',
        'Sales Workflow': 'Alur Penjualan',
        'Procurement': 'Pengadaan',
        'Warehouse & Stock': 'Gudang & Stok',
        'Manufacturing': 'Manufaktur',
        'Projects & Tasks': 'Proyek & Tugas',
        'Helpdesk Tickets': 'Tiket Bantuan',
        'Operations': 'Operasional',
        'HR & Recruitment': 'SDM & Rekrutmen',
        'Accounting': 'Akuntansi',
        'Reports': 'Laporan',
        'Settings': 'Pengaturan',
        'Multi-Company': 'Multi-Perusahaan',

        // Sidebar – Items
        'Products': 'Produk',
        'Customers': 'Pelanggan',
        'Suppliers': 'Pemasok',
        'Warehouses': 'Gudang',
        'CRM Pipeline': 'Pipeline CRM',
        'POS Terminal ↗': 'Terminal POS ↗',
        'Cabang / Store POS': 'Cabang / Toko POS',
        'Member & Loyalty': 'Member & Loyalitas',
        'Promo & Diskon': 'Promo & Diskon',
        'Laporan POS & Shift': 'Laporan POS & Shift',
        'Quotations': 'Penawaran',
        'Sales Orders': 'Pesanan Penjualan',
        'Invoices / Sales': 'Faktur / Penjualan',
        'Delivery Orders': 'Surat Jalan',
        'Product Returns': 'Retur Produk',
        'Credit/Debit Notes': 'Nota Kredit/Debit',
        'Subscriptions': 'Langganan',
        'Rentals': 'Penyewaan',
        'Purchase Requests': 'Permintaan Pembelian',
        'RFQs': 'Permintaan Penawaran Harga',
        'Purchase Orders': 'Order Pembelian',
        'Good Receipts': 'Penerimaan Barang',
        'Stock Balance': 'Saldo Stok',
        'Stock Valuation / LC': 'Valuasi Stok / LC',
        'Kalkulator HPP Impor': 'Kalkulator HPP Impor',
        'Quality Control (QC)': 'Kontrol Kualitas (QC)',
        'Barcode Scanner (SKU)': 'Pemindai Barcode (SKU)',
        'Warehouse Transfers': 'Transfer Gudang',
        'Reordering Rules': 'Aturan Pemesanan Ulang',
        'Advanced Logistics': 'Logistik Lanjutan',
        'Bill of Materials (BOM)': 'Daftar Bahan (BOM)',
        'Production Orders': 'Order Produksi',
        'Product Lifecycle (PLM)': 'Siklus Hidup Produk (PLM)',
        'Advanced Mfg (MRP II)': 'Manufaktur Lanjutan (MRP II)',
        'Fleet Management': 'Manajemen Armada',
        'Equipment Maintenance': 'Perawatan Peralatan',
        'Field Service (FSM)': 'Layanan Lapangan (FSM)',
        'HR & Payroll': 'SDM & Penggajian',
        'Expenses Claim': 'Klaim Pengeluaran',
        'Recruitment (ATS)': 'Rekrutmen (ATS)',
        'Performance Reviews': 'Penilaian Kinerja',
        'Work Schedules': 'Jadwal Kerja',
        'Journal Ledger': 'Buku Besar Jurnal',
        'Bank Reconciliation': 'Rekonsiliasi Bank',
        'Tax Management': 'Manajemen Pajak',
        'Multi-Currency': 'Multi-Mata Uang',
        'Budgeting': 'Anggaran',
        'Workflow Approvals': 'Persetujuan Alur Kerja',
        'HPP Calculator': 'Kalkulator HPP',
        'Advanced Accounting': 'Akuntansi Lanjutan',
        'Website & CMS': 'Website & CMS',
        'E-Commerce': 'E-Commerce',
        'Email Marketing': 'Email Marketing',
        'Marketing Automation': 'Otomasi Pemasaran',
        'Document Archive': 'Arsip Dokumen',
        'E-Sign Requests': 'Permintaan E-Sign',

        // Content Manager
        'Data Banner': 'Data Banner',
        'Data About us': 'Data Tentang Kami',
        'Data Our Services': 'Data Layanan Kami',
        'Data Our Value': 'Data Nilai Kami',
        'Data Gallery': 'Data Galeri',
        'Data Our Client': 'Data Klien Kami',
        'Data Tagline': 'Data Tagline',
        'Data Testimoni': 'Data Testimoni',
        'Data Contact Us': 'Data Kontak Kami',
        'Data Themes Selection': 'Pilihan Tema',

        // Config
        'Config Banner': 'Konfigurasi Banner',
        'Config About us': 'Konfigurasi Tentang Kami',
        'Config Our Services': 'Konfigurasi Layanan Kami',
        'Config Our Gallery': 'Konfigurasi Galeri Kami',
        'Config News': 'Konfigurasi Berita',
        'Config Value': 'Konfigurasi Nilai',
        'Config Testimoni': 'Konfigurasi Testimoni',
        'Config Tagline': 'Konfigurasi Tagline',
        'Config Contact Us': 'Konfigurasi Kontak Kami',

        // Common UI
        'Add Product': 'Tambah Produk',
        'Add New Product': 'Tambah Produk Baru',
        'Edit Product': 'Edit Produk',
        'Product Management': 'Manajemen Produk',
        'Manage your ERP inventory catalog, details, prices, and stock limits.': 'Kelola katalog inventaris ERP, detail, harga, dan batas stok.',
        'Search by name or code...': 'Cari berdasarkan nama atau kode...',
        'All Categories': 'Semua Kategori',
        'Product Code': 'Kode Produk',
        'Image': 'Gambar',
        'Name': 'Nama',
        'Category': 'Kategori',
        'Price': 'Harga',
        'Stock': 'Stok',
        'Min Stock': 'Stok Min',
        'Actions': 'Aksi',
        'Edit': 'Ubah',
        'Delete': 'Hapus',
        'No products found match your search criteria.': 'Tidak ada produk yang cocok dengan kriteria pencarian Anda.',
        'Product Name': 'Nama Produk',
        'Select Category': 'Pilih Kategori',
        'Price (IDR)': 'Harga (IDR)',
        'Unit': 'Satuan',
        'Current Stock': 'Stok Saat Ini',
        'Min. Alert Stock': 'Stok Min. Alert',
        'Product Image (Optional)': 'Gambar Produk (Opsional)',
        'New image preview:': 'Pratinjau gambar baru:',
        'Current image:': 'Gambar saat ini:',
        'Cancel': 'Batal',
        'Save Changes': 'Simpan Perubahan',
        'Save': 'Simpan',
        'Close': 'Tutup',
        'Create': 'Buat',
        'Search': 'Cari',
        'Filter': 'Filter',
        'Export': 'Ekspor',
        'Import': 'Impor',
        'Refresh': 'Segarkan',
        'Loading...': 'Memuat...',
        'Uploading...': 'Mengunggah...',
        'Success!': 'Berhasil!',
        'Error!': 'Kesalahan!',
        'Warning!': 'Peringatan!',
        'Uncategorized': 'Tanpa Kategori',
        'Administrator': 'Administrator',
        'Total': 'Total',
        'Status': 'Status',
        'Date': 'Tanggal',
        'Description': 'Deskripsi',
        'Amount': 'Jumlah',
        'Notes': 'Catatan',
        'Active': 'Aktif',
        'Inactive': 'Nonaktif',
        'Yes': 'Ya',
        'No': 'Tidak',
        'Confirm': 'Konfirmasi',
        'Are you sure?': 'Apakah Anda yakin?',
        'Back': 'Kembali',
        'Next': 'Selanjutnya',
        'Previous': 'Sebelumnya',
        'Showing': 'Menampilkan',
        'to': 'sampai',
        'of': 'dari',
        'results': 'hasil',
        'per page': 'per halaman',
        'No data available': 'Tidak ada data tersedia',
        'No results found': 'Tidak ada hasil ditemukan',

        // Member Manager
        'Member Management': 'Manajemen Member',
        'Manage your POS loyalty members, tiers, and point rewards.': 'Kelola member loyalitas POS, tier, dan reward poin.',
        'Add Member': 'Tambah Member',
        'Member Code': 'Kode Member',
        'Phone': 'Telepon',
        'Email': 'Email',
        'Tier': 'Tier',
        'Points': 'Poin',
        'Total Spending': 'Total Belanja',
        'Joined': 'Bergabung',

        // Store Manager
        'Store Management': 'Manajemen Store',

        // Promo Manager
        'Promo & Voucher Management': 'Manajemen Promo & Voucher',
        'Promotions': 'Promosi',
        'Vouchers': 'Voucher',
        'Add Promotion': 'Tambah Promosi',
        'Generate Vouchers': 'Buat Voucher',
        'Discount Type': 'Jenis Diskon',
        'Discount Value': 'Nilai Diskon',
        'Min Purchase': 'Min. Pembelian',
        'Valid From': 'Berlaku Dari',
        'Valid Until': 'Berlaku Sampai',
        'Percentage': 'Persentase',
        'Fixed Amount': 'Nominal Tetap',

        // POS Report
        'POS Reports': 'Laporan POS',
        'Overview': 'Ringkasan',
        'Transactions': 'Transaksi',
        'Profit & Loss': 'Laba Rugi',
        'EOD / Shift': 'EOD / Shift',
        'Total Revenue': 'Total Pendapatan',
        'Total Transactions': 'Total Transaksi',
        'Average / Transaction': 'Rata-rata / Transaksi',
        'Total Discount': 'Total Diskon',
        'Daily Revenue': 'Pendapatan Harian',
        'Payment Method': 'Metode Pembayaran',
        'Transaction Number': 'No. Transaksi',
        'Time': 'Waktu',
        'Cashier': 'Kasir',
        'Member': 'Member',
        'Method': 'Metode',
        'Top 10 Best Selling Products': 'Top 10 Produk Terlaris',
        'Product': 'Produk',
        'Qty Sold': 'Qty Terjual',
        'Revenue': 'Pendapatan',
    };

    // Build reverse dict (ID → EN)
    const reverseDict = {};
    for (const [en, id] of Object.entries(dict)) {
        reverseDict[id] = en;
    }

    let currentLang = localStorage.getItem('erp_lang') || 'en';

    // ── Core Walker ─────────────────────────────────────────────
    function translateNode(node, fromDict) {
        if (node.nodeType === Node.TEXT_NODE) {
            const trimmed = node.textContent.trim();
            if (trimmed && fromDict[trimmed]) {
                node.textContent = node.textContent.replace(trimmed, fromDict[trimmed]);
            }
            return;
        }
        // Placeholders
        if (node.placeholder) {
            const ph = node.placeholder.trim();
            if (fromDict[ph]) node.placeholder = fromDict[ph];
        }
        // Title attributes
        if (node.title) {
            const t = node.title.trim();
            if (fromDict[t]) node.title = fromDict[t];
        }
    }

    function walkAndTranslate(root, fromDict) {
        const walker = document.createTreeWalker(
            root,
            NodeFilter.SHOW_TEXT | NodeFilter.SHOW_ELEMENT,
            null,
            false
        );
        let node;
        while ((node = walker.nextNode())) {
            translateNode(node, fromDict);
        }
    }

    function applyTranslation() {
        if (currentLang === 'id') {
            walkAndTranslate(document.body, dict);        // EN → ID
        } else {
            walkAndTranslate(document.body, reverseDict); // ID → EN
        }
        // Update the toggle button text
        const btn = document.getElementById('lang-toggle-label');
        if (btn) btn.textContent = currentLang === 'id' ? 'ID' : 'EN';
        const flag = document.getElementById('lang-toggle-flag');
        if (flag) flag.textContent = currentLang === 'id' ? '🇮🇩' : '🇬🇧';
    }

    function setLang(lang) {
        // First reset to source language
        if (currentLang === 'id') {
            walkAndTranslate(document.body, reverseDict);
        }
        currentLang = lang;
        localStorage.setItem('erp_lang', lang);
        if (lang === 'id') {
            walkAndTranslate(document.body, dict);
        }
        const btn = document.getElementById('lang-toggle-label');
        if (btn) btn.textContent = lang === 'id' ? 'ID' : 'EN';
        const flag = document.getElementById('lang-toggle-flag');
        if (flag) flag.textContent = lang === 'id' ? '🇮🇩' : '🇬🇧';
    }

    function toggle() {
        setLang(currentLang === 'en' ? 'id' : 'en');
    }

    function init() {
        // Always set the flag/label to match persisted language
        const btn = document.getElementById('lang-toggle-label');
        if (btn) btn.textContent = currentLang === 'id' ? 'ID' : 'EN';
        const flag = document.getElementById('lang-toggle-flag');
        if (flag) flag.textContent = currentLang === 'id' ? '🇮🇩' : '🇬🇧';

        // Apply saved language on load
        if (currentLang === 'id') {
            applyTranslation();
        }

        // Re-translate after Livewire updates
        document.addEventListener('livewire:navigated', () => {
            setTimeout(() => { if (currentLang === 'id') applyTranslation(); }, 100);
        });

        // Observe DOM mutations for Livewire re-renders
        const observer = new MutationObserver((mutations) => {
            if (currentLang !== 'id') return;
            for (const m of mutations) {
                for (const added of m.addedNodes) {
                    if (added.nodeType === Node.ELEMENT_NODE) {
                        walkAndTranslate(added, dict);
                    }
                }
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    return { init, toggle, setLang, currentLang: () => currentLang };
})();

// Boot on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => ERPTranslator.init());
