<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MenuSetting extends Model
{
    protected $fillable = ['route_name', 'label', 'group', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all active route names (cached for performance).
     */
    public static function getActiveRoutes(): array
    {
        return Cache::remember('menu_active_routes', 60 * 5, function () {
            return static::where('is_active', true)->pluck('route_name')->toArray();
        });
    }

    /**
     * Get all menu labels mapped by route_name (cached for performance).
     */
    public static function getMenuLabels(): array
    {
        return Cache::remember('menu_labels_map', 60 * 5, function () {
            return static::pluck('label', 'route_name')->toArray();
        });
    }

    /**
     * Check if a specific route is active.
     */
    public static function isRouteActive(string $routeName): bool
    {
        return in_array($routeName, static::getActiveRoutes());
    }

    /**
     * Get all searchable menus with routes, URLs, groups, and keywords for topbar search.
     */
    public static function getSearchableMenus(): array
    {
        return Cache::remember('menu_searchable_list', 60 * 10, function () {
            $menus = static::where('is_active', true)
                ->orderBy('group')
                ->orderBy('sort_order')
                ->get();

            $list = [];
            $keywordsMap = [
                'dashboard' => 'home beranda ringkasan statistik summary',
                'products' => 'barang produk items master barang katalog daftar barang',
                'categories' => 'kategori golongan jenis klasifikasi master kategori',
                'customers' => 'pelanggan klien customer pembeli mitra langganan',
                'suppliers' => 'pemasok supplier vendor rekanan penyedia',
                'warehouses' => 'gudang warehouse lokasi depo rak penyimpanan storage',
                'crm' => 'pipeline prospek leads peluang deals customer relationship crm',
                'pos' => 'kasir point of sale toko kasir penjualan langsung pos terminal kasir',
                'pos-stores' => 'cabang outlet store toko kasir cabang pos stores',
                'pos-members' => 'member keanggotaan loyalty pelanggan tetap point kartu member',
                'pos-promos' => 'promo promosi diskon potongan voucher kupon promo discount',
                'pos-reports' => 'laporan pos shift kasir kas closing harian pos reports',
                'sales-quotations' => 'penawaran sph quotation estimasi harga penawaran harga quote',
                'sales-orders' => 'so sales order pesanan penjualan order penjualan',
                'sales' => 'invoice sales faktur tagihan penjualan kwitansi jual invoice',
                'delivery-orders' => 'surat jalan do delivery order surat pengiriman barang jalan logistik kirim',
                'returns' => 'retur return pengembalian barang rusak customer supplier kembalian barang',
                'invoices' => 'nota debit nota kredit debit note credit note memo penyesuaian invoice retur',
                'subscriptions' => 'langganan subscription paket berkala billing tagihan berulang',
                'rentals' => 'sewa rental penyewaan rental alat leasing pinjam rental',
                'bom' => 'bom bill of materials formula resep komposisi bahan baku manufaktur bill',
                'production-orders' => 'perintah kerja spk production order wo work order produksi pabrikasi',
                'plm' => 'product lifecycle plm siklus hidup produk r&d inovasi desain produk',
                'advanced-manufacturing' => 'mrp mrp ii manufaktur lanjutan kapasitas mesin routing kerja work center',
                'good-receipts' => 'penerimaan barang lpbg grn good receipt receipt inbound masuk gudang terima barang',
                'inventory' => 'stok balance saldo stok kartu stok persediaan barang opname mutasi gudang stok',
                'stock-valuation' => 'valuasi stok landed cost nilai persediaan harga pokok persediaan fifo average',
                'hpp-calculator' => 'kalkulator hpp impor bea masuk pph ppn biaya landed cost kalkulasi modal',
                'quality-control' => 'qc quality control kualitas inspeksi uji coba sortir layak afkir mutu',
                'barcode-scanner' => 'scan barcode sku scanner scan label qr code kode barang cetak label',
                'warehouse-transfers' => 'transfer gudang mutasi antar gudang pindah barang mutasi internal transfer',
                'reordering-rules' => 'reorder point minimum stok rop safety stock pemesanan ulang otomatis restock',
                'advanced-logistics' => 'logistik armada rute pengiriman ekspedisi tracking muatan logistic',
                'hr' => 'karyawan pegawai hrd payroll gaji slip gaji sdm tunjangan bonus upah',
                'expenses' => 'klaim claim expenses pengeluaran kasbon reimbursement operasional bon',
                'recruitment' => 'rekrutmen pelamar loker job opening lowongan ats seleksi interview recruitment',
                'appraisals' => 'penilaian kinerja kpi kpa evaluasi kerja performa raport pegawai appraisal',
                'schedules' => 'jadwal kerja shift roster jam kerja kalender piket absensi kalender kerja',
                'accounting' => 'akuntansi buku besar general ledger coa jurnal neraca laba rugi chart of accounts finance',
                'cash-bank' => 'kas dan bank cash bank mutasi rekening giro transfer kas keluar kas masuk petty cash',
                'accounts-receivable' => 'piutang ar buku pembantu piutang jatuh tempo penagihan kartu piutang',
                'accounts-payable' => 'hutang ap utang usaha buku pembantu hutang jatuh tempo bayar utang tagihan supplier',
                'bank-reconciliation' => 'rekonsiliasi bank pencocokan mutasi koran bank statement rekening koran giro',
                'taxes' => 'pajak efaktur e-faktur ppn pph 21 pph 23 spt masa bukti potong tax',
                'currencies' => 'valas mata uang multi currency kurs tukar kurs dollar konversi uang asing',
                'budgets' => 'anggaran budget rencana anggaran biaya rab belanja target realisasi budgeting',
                'approvals' => 'persetujuan approval otorisasi hierarki acc sign off verifikasi approve',
                'advanced-accounting' => 'akuntansi lanjutan konsolidasi amortisasi depresiasi aset tetap aktiva',
                'purchase-requests' => 'pr purchase request permintaan pembelian pengajuan beli kebutuhan internal minta barang',
                'rfqs' => 'rfq penawaran supplier quotation vendor permintaan penawaran komparasi harga tender',
                'purchases' => 'po purchase order pesanan pembelian order beli belanja barang suplier order beli',
                'fleet' => 'kendaraan armada mobil motor truk bensin servis kilometer pajak stnk bpkb armada',
                'maintenance' => 'perawatan pemeliharaan maintenance mesin alat genset servis berkala suku cadang perbaikan',
                'fsm' => 'field service fsm teknisi kunjungan service lapangan tiket perbaikan on site field',
                'content-manager' => 'konten cms banner web about us testimoni profil galeri landing page website',
                'config-basic-manager' => 'konfigurasi profil perusahaan visi misi kontak media sosial setup website basic config',
                'website-cms' => 'website web cms artikel blog halaman post berita publikasi landing page cms',
                'ecommerce' => 'toko online olshop e-commerce keranjang checkout payment gateway ecomm',
                'marketing' => 'email blast kampanye email marketing promosi newsletter broadcast kirim email',
                'marketing-automation' => 'automasi marketing autoresponder workflow funnel trigger leads otomatis',
                'discuss' => 'chat pesan discuss obrolan forum diskusi grup percakapan internal inbox',
                'documents' => 'dokumen arsip file berkas arsip digital upload pdf surat berkas document',
                'sign' => 'e-sign ttd tanda tangan elektronik paraf surat pengesahan digital sign tandatangan',
                'projects-manager' => 'proyek task project tugas kanban gantt chart milestone tim kerja task manager',
                'helpdesk' => 'tiket bantuan keluhan support customer service komplain pengaduan helpdesk ticket',
                'reports' => 'laporan eksekutif analitik ringkasan rekapitulasi export pdf excel bi executive reports',
                'multi-company' => 'anak perusahaan entitas cabang legal company holding group multi cabang',
                'settings' => 'pengaturan sistem konfigurasi settings hak akses user role aplikasi general setelan',
                'manage-menu' => 'kelola menu aktivasi menu urutan menu visibility hak akses menu setting manajemen menu',
            ];

            foreach ($menus as $m) {
                if (\Illuminate\Support\Facades\Route::has($m->route_name)) {
                    $list[] = [
                        'name' => $m->label,
                        'route' => $m->route_name,
                        'url' => route($m->route_name),
                        'group' => $m->group ?? 'General',
                        'keywords' => strtolower(($keywordsMap[$m->route_name] ?? '') . ' ' . $m->label . ' ' . ($m->group ?? '')),
                    ];
                }
            }

            // Also add tab shortcuts for Content Manager & Config Basic
            if (in_array('content-manager', $menus->pluck('route_name')->toArray()) && \Illuminate\Support\Facades\Route::has('content-manager')) {
                $contentTabs = [
                    ['name' => 'Data Banner (Content)', 'tab' => 'banner', 'keywords' => 'banner slider promosi gambar depan content web'],
                    ['name' => 'Data About Us (Content)', 'tab' => 'about', 'keywords' => 'tentang kami tentang perusahaan profil about us content'],
                    ['name' => 'Data Services (Content)', 'tab' => 'service', 'keywords' => 'layanan jasa service kami content web'],
                    ['name' => 'Data Gallery (Content)', 'tab' => 'gallery', 'keywords' => 'galeri foto dokumentasi gallery content'],
                    ['name' => 'Data Testimoni (Content)', 'tab' => 'testimoni', 'keywords' => 'testimoni ulasan review testimoni klien customer content'],
                    ['name' => 'Data Contact Us (Content)', 'tab' => 'contact', 'keywords' => 'kontak hubungi kami alamat nomor telepon contact content'],
                ];
                foreach ($contentTabs as $tab) {
                    $list[] = [
                        'name' => $tab['name'],
                        'route' => 'content-manager',
                        'url' => route('content-manager', ['tab' => $tab['tab']]),
                        'group' => 'Content',
                        'keywords' => strtolower($tab['keywords'] . ' content manager ' . $tab['name']),
                    ];
                }
            }

            return $list;
        });
    }

    /**
     * Clear the cached active routes and labels.
     */
    public static function clearCache(): void
    {
        Cache::forget('menu_active_routes');
        Cache::forget('menu_labels_map');
        Cache::forget('menu_searchable_list');
    }
}
