# Sistem Informasi Akuntansi (SIA) — Dokumentasi Fitur Lengkap

> Dokumen ini mencakup seluruh fitur modul Sistem Informasi Akuntansi (SIA) yang digunakan dalam sistem ERP untuk berbagai jenis usaha: cafe, toko retail, distributor, maupun perusahaan startup.

---

## Daftar Isi

1. [Buku Besar (General Ledger)](#1-buku-besar-general-ledger)
2. [Kas & Bank](#2-kas--bank-cash--bank-management)
3. [Piutang (Accounts Receivable)](#3-piutang-accounts-receivable)
4. [Hutang (Accounts Payable)](#4-hutang-accounts-payable)
5. [Persediaan (Inventory)](#5-persediaan-inventory)
6. [Penjualan (Sales)](#6-penjualan-sales)
7. [Pembelian (Purchasing)](#7-pembelian-purchasing)
8. [Penggajian (Payroll)](#8-penggajian-payroll)
9. [Aset Tetap (Fixed Assets)](#9-aset-tetap-fixed-assets)
10. [Laporan Keuangan (Financial Reporting)](#10-laporan-keuangan-financial-reporting)
11. [Perpajakan (Tax Management)](#11-perpajakan-tax-management)
12. [Anggaran (Budgeting)](#12-anggaran-budgeting)
13. [Modul Pendukung (Supporting Modules)](#13-modul-pendukung-supporting-modules)
14. [Prioritas Implementasi](#14-prioritas-implementasi)

---

## 1. Buku Besar (General Ledger)

Modul inti dari seluruh sistem akuntansi. Semua transaksi keuangan bermuara ke modul ini.

### Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| Chart of Accounts (CoA) | Daftar akun lengkap: Aset, Liabilitas, Ekuitas, Pendapatan, Beban |
| Jurnal Umum | Pencatatan transaksi manual debit/kredit |
| Jurnal Penyesuaian | Akrual, depresiasi, prepaid, penyisihan piutang |
| Jurnal Penutup | Menutup akun nominal (pendapatan & beban) di akhir periode |
| Buku Besar | Ledger per akun dengan riwayat mutasi lengkap |
| Neraca Saldo | Trial balance sebelum & sesudah penyesuaian |
| Multi-Currency | Pencatatan & konversi transaksi valuta asing |
| Multi-Company | Konsolidasi laporan untuk grup usaha / holding |

### Struktur CoA (Contoh)

```
1xxx  Aset
  11xx  Aset Lancar
    1101  Kas
    1102  Bank BCA
    1103  Piutang Usaha
    1104  Persediaan Barang
  12xx  Aset Tetap
    1201  Peralatan
    1202  Akumulasi Penyusutan Peralatan

2xxx  Liabilitas
  21xx  Liabilitas Jangka Pendek
    2101  Hutang Usaha
    2102  Hutang Pajak

3xxx  Ekuitas
  3101  Modal Disetor
  3102  Laba Ditahan

4xxx  Pendapatan
  4101  Penjualan

5xxx  Beban Pokok Penjualan (HPP)
  5101  Harga Pokok Barang Terjual

6xxx  Beban Operasional
  6101  Beban Gaji
  6102  Beban Sewa
  6103  Beban Listrik & Air
```

---

## 2. Kas & Bank (Cash & Bank Management)

Mengelola seluruh arus uang masuk dan keluar baik tunai maupun rekening bank.

### Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| Kas Kecil (Petty Cash) | Pengelolaan dana operasional harian dalam jumlah kecil |
| Buku Kas | Pencatatan penerimaan & pengeluaran kas secara kronologis |
| Buku Bank | Pencatatan mutasi per rekening bank |
| Rekonsiliasi Bank | Mencocokkan saldo buku perusahaan dengan mutasi bank |
| Transfer Antar Rekening | Pencatatan perpindahan dana antar rekening internal |
| Manajemen Cek / Giro | Pelacakan cek/giro yang diterbitkan maupun diterima |

### Alur Rekonsiliasi Bank

```
Saldo Buku Perusahaan
  (+) Deposit in Transit (setoran dalam perjalanan)
  (-) Outstanding Check (cek yang belum dicairkan)
  (+/-) Error Koreksi Buku
= Saldo yang Disesuaikan

Saldo Mutasi Bank
  (+) Jasa Giro / Bunga Bank
  (-) Biaya Administrasi Bank
  (+/-) Error Koreksi Bank
= Saldo yang Disesuaikan
```

---

## 3. Piutang (Accounts Receivable)

Mengelola seluruh tagihan kepada pelanggan beserta pemantauan pembayarannya.

### Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| Master Customer | Data lengkap pelanggan beserta limit kredit |
| Invoice / Faktur Penjualan | Penerbitan tagihan kepada pelanggan |
| Penerimaan Pembayaran | Pencatatan pelunasan piutang |
| Kredit Memo / Retur | Pengurangan piutang akibat retur barang |
| Aging Piutang | Laporan umur piutang: 0-30, 31-60, 61-90, >90 hari |
| Limit Kredit | Kontrol batas maksimum hutang pelanggan |
| Reminder / Dunning Letter | Notifikasi otomatis tagihan jatuh tempo |

### Contoh Laporan Aging Piutang

```
Pelanggan       | Total      | 0-30 hr   | 31-60 hr  | 61-90 hr  | >90 hr
----------------|------------|-----------|-----------|-----------|--------
Toko Maju       | 5.000.000  | 3.000.000 | 2.000.000 |           |
CV Berkah       | 8.500.000  | 1.500.000 | 3.000.000 | 4.000.000 |
PT Sumber Jaya  | 2.000.000  |           |           | 500.000   | 1.500.000
```

---

## 4. Hutang (Accounts Payable)

Mengelola seluruh kewajiban pembayaran kepada pemasok/vendor.

### Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| Master Supplier | Data lengkap vendor/pemasok |
| Purchase Invoice | Pencatatan faktur dari supplier |
| Pembayaran Hutang | Proses pelunasan kewajiban kepada supplier |
| Debit Memo / Retur | Pengurangan hutang akibat retur barang ke supplier |
| Aging Hutang | Laporan umur hutang untuk perencanaan pembayaran |
| Jadwal Pembayaran | Payment schedule berdasarkan due date |

---

## 5. Persediaan (Inventory)

Mengelola stok barang mulai dari penerimaan, pengeluaran, hingga penilaian.

### Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| Master Barang / SKU | Data produk lengkap dengan kode, satuan, kategori |
| Metode Penilaian Stok | FIFO, LIFO, Average Cost, Specific Identification |
| Penerimaan Barang (GRN) | Good Receipt Note dari supplier |
| Pengeluaran Barang | Pengiriman ke pelanggan atau pemakaian internal |
| Stock Opname | Penghitungan fisik & penyesuaian selisih stok |
| Transfer Stok | Perpindahan barang antar gudang / lokasi |
| Reorder Point | Notifikasi otomatis saat stok mendekati minimum |
| Bill of Materials (BOM) | Komposisi bahan baku untuk usaha produksi |

### Metode Penilaian Stok

| Metode | Keterangan | Cocok untuk |
|--------|-----------|-------------|
| FIFO | Barang masuk pertama, keluar pertama | Produk dengan tanggal kadaluarsa |
| Average Cost | Harga rata-rata tertimbang | Umum digunakan, sederhana |
| LIFO | Barang masuk terakhir, keluar pertama | Jarang dipakai di Indonesia |
| Specific ID | Identifikasi biaya per unit | Barang unik/mahal (kendaraan, mesin) |

---

## 6. Penjualan (Sales)

Mengelola seluruh proses penjualan dari penawaran hingga pembayaran diterima.

### Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| Quotation | Penawaran harga kepada calon pelanggan |
| Sales Order (SO) | Konfirmasi pesanan penjualan |
| Delivery Order (DO) | Surat jalan pengiriman barang |
| Invoice Penjualan | Tagihan resmi kepada pelanggan |
| Retur Penjualan | Pengembalian barang dari pelanggan |
| Price List & Diskon | Daftar harga bertingkat & manajemen diskon |
| Point of Sale (POS) | Kasir digital untuk cafe/retail/toko |
| Komisi Salesman | Perhitungan komisi otomatis berdasarkan penjualan |

### Alur Proses Penjualan

```
Quotation → Sales Order → Delivery Order → Invoice → Pembayaran
     ↓             ↓              ↓             ↓           ↓
  Penawaran    Konfirmasi     Pengiriman    Penagihan   Pelunasan
  Harga        Pesanan        Barang        Piutang     Piutang
```

---

## 7. Pembelian (Purchasing)

Mengelola proses pengadaan barang/jasa dari kebutuhan hingga pembayaran ke supplier.

### Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| Purchase Requisition (PR) | Permintaan pembelian internal dari departemen |
| Purchase Order (PO) | Surat pesanan resmi kepada supplier |
| Penerimaan & Verifikasi | Pengecekan barang diterima sesuai PO |
| Invoice Pembelian | Pencatatan tagihan dari supplier |
| Retur Pembelian | Pengembalian barang ke supplier |
| Vendor Performance | Penilaian kinerja supplier (harga, ketepatan, kualitas) |

### Alur Proses Pembelian

```
PR → PO → Penerimaan Barang → Invoice Masuk → Pembayaran
  ↓     ↓         ↓                ↓               ↓
Butuh  Order    GRN + QC        Hutang AP       Pelunasan
Barang Kirim    Gudang          Tercatat        Hutang
```

---

## 8. Penggajian (Payroll)

Menghitung dan mendistribusikan gaji karyawan beserta komponen pajaknya.

### Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| Master Karyawan | Data lengkap karyawan: jabatan, departemen, NPWP, BPJS |
| Komponen Gaji | Gaji pokok, tunjangan tetap/tidak tetap, lembur, potongan |
| Slip Gaji | Cetak/kirim slip gaji digital per karyawan |
| PPh 21 | Perhitungan pajak penghasilan karyawan otomatis |
| BPJS Ketenagakerjaan | Iuran JHT, JP, JKK, JKM |
| BPJS Kesehatan | Iuran kesehatan karyawan & perusahaan |
| THR & Bonus | Perhitungan Tunjangan Hari Raya & bonus tahunan |
| Transfer Gaji | Integrasi payroll ke rekening bank karyawan |

### Struktur Slip Gaji

```
SLIP GAJI — PERIODE: [Bulan/Tahun]
Nama         : [Nama Karyawan]
Jabatan      : [Jabatan]
Departemen   : [Departemen]

PENGHASILAN                    POTONGAN
-------------------------      -------------------------
Gaji Pokok     : Rp xxx.xxx    BPJS Kes (1%)  : Rp x.xxx
Tunjangan Makan: Rp xxx.xxx    BPJS TK (2%)   : Rp x.xxx
Tunjangan Transp: Rp xxx.xxx   PPh 21         : Rp x.xxx
Lembur         : Rp xxx.xxx    Pinjaman        : Rp x.xxx
-------------------------      -------------------------
Total Penghasilan: Rp xxx.xxx  Total Potongan : Rp x.xxx

GAJI BERSIH (Take Home Pay)    : Rp xxx.xxx
```

---

## 9. Aset Tetap (Fixed Assets)

Mengelola aset jangka panjang perusahaan dari perolehan hingga penghapusan.

### Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| Master Aset | Kendaraan, mesin, peralatan, komputer, bangunan |
| Penyusutan (Depresiasi) | Garis Lurus, Saldo Menurun, Unit Produksi |
| Revaluasi Aset | Penyesuaian nilai aset berdasarkan harga pasar |
| Disposal / Penghapusan | Penjualan, pemusnahan, atau donasi aset |
| Jadwal Pemeliharaan | Perawatan berkala & history servis aset |

### Metode Depresiasi

| Metode | Formula | Cocok untuk |
|--------|---------|-------------|
| Garis Lurus | (Harga Perolehan - Nilai Sisa) / Umur Ekonomis | Aset umum |
| Saldo Menurun | Nilai Buku × Tarif % | Kendaraan, elektronik |
| Unit Produksi | (HP - NS) / Total Unit × Unit Periode | Mesin produksi |

---

## 10. Laporan Keuangan (Financial Reporting)

Menghasilkan laporan keuangan standar sesuai PSAK / IFRS.

### Daftar Laporan

| Laporan | Deskripsi |
|---------|-----------|
| Laporan Laba Rugi (P&L) | Pendapatan dikurangi beban dalam satu periode |
| Neraca (Balance Sheet) | Posisi aset, liabilitas, ekuitas pada tanggal tertentu |
| Laporan Perubahan Ekuitas | Pergerakan modal selama periode berjalan |
| Laporan Arus Kas | Cash flow: operasional, investasi, pendanaan |
| Laporan HPP | Rincian Harga Pokok Penjualan |
| Laporan per Departemen | Laba/rugi per divisi atau cost center |
| Laporan Perbandingan | Periode lalu vs sekarang vs anggaran |
| Laporan Konsolidasi | Gabungan laporan seluruh entitas dalam grup |

### Struktur Laporan Arus Kas

```
LAPORAN ARUS KAS
Periode: [Bulan/Tahun]

I. AKTIVITAS OPERASI
   Penerimaan dari pelanggan          : Rp xxx.xxx.xxx
   Pembayaran kepada supplier         : (Rp xxx.xxx.xxx)
   Pembayaran gaji karyawan           : (Rp xxx.xxx.xxx)
   Pembayaran pajak                   : (Rp xxx.xxx.xxx)
   Kas Bersih dari Aktivitas Operasi  : Rp xxx.xxx.xxx

II. AKTIVITAS INVESTASI
   Pembelian aset tetap               : (Rp xxx.xxx.xxx)
   Hasil penjualan aset               : Rp xxx.xxx.xxx
   Kas Bersih dari Aktivitas Investasi: (Rp xxx.xxx.xxx)

III. AKTIVITAS PENDANAAN
   Penerimaan pinjaman bank           : Rp xxx.xxx.xxx
   Pembayaran cicilan pinjaman        : (Rp xxx.xxx.xxx)
   Kas Bersih dari Aktivitas Pendanaan: Rp xxx.xxx.xxx

KENAIKAN (PENURUNAN) KAS BERSIH      : Rp xxx.xxx.xxx
Saldo Kas Awal Periode               : Rp xxx.xxx.xxx
SALDO KAS AKHIR PERIODE              : Rp xxx.xxx.xxx
```

---

## 11. Perpajakan (Tax Management)

Mengelola kewajiban perpajakan perusahaan secara otomatis dan terintegrasi.

### Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| PPN Masukan & Keluaran | Pencatatan & perhitungan PPN 12% |
| Faktur Pajak (e-Faktur) | Penerbitan faktur pajak elektronik sesuai format DJP |
| SPT Masa PPN | Penyusunan laporan PPN bulanan |
| PPh 21 | Pajak penghasilan karyawan |
| PPh 22 | Pajak atas pembelian barang tertentu |
| PPh 23 | Pajak jasa, sewa, royalti |
| PPh 25 | Cicilan pajak tahunan |
| PPh 29 | Pajak tahunan badan usaha |
| Rekonsiliasi Pajak | Pencocokan objek pajak dengan laporan keuangan |

---

## 12. Anggaran (Budgeting)

Merencanakan, memantau, dan menganalisis kinerja keuangan terhadap target.

### Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| Budget Planning | Penyusunan anggaran per departemen / proyek / akun |
| Budget vs Realisasi | Perbandingan rencana vs aktual secara real-time |
| Forecasting | Proyeksi keuangan berdasarkan tren historis |
| Variance Analysis | Analisis selisih anggaran: favorable vs unfavorable |
| Rolling Forecast | Pembaruan proyeksi secara berkelanjutan |

### Contoh Tabel Budget vs Realisasi

```
Akun               | Budget (Rp)  | Realisasi (Rp) | Variance     | %
-------------------|--------------|----------------|--------------|------
Pendapatan         | 50.000.000   | 47.500.000     | (2.500.000)  | -5%
Beban Gaji         | 15.000.000   | 14.800.000     | 200.000      | +1%
Beban Marketing    | 5.000.000    | 6.200.000      | (1.200.000)  | -24%
Beban Operasional  | 10.000.000   | 9.500.000      | 500.000      | +5%
```

---

## 13. Modul Pendukung (Supporting Modules)

### Fitur Pendukung

| Fitur | Deskripsi |
|-------|-----------|
| Cost Center / Profit Center | Alokasi biaya & pendapatan per divisi/proyek |
| Tutup Buku (Period Closing) | Proses penutupan periode bulanan & tahunan |
| Audit Trail | Log lengkap setiap transaksi: siapa, kapan, apa yang diubah |
| Role & Permission | Hak akses berbasis jabatan/departemen |
| Integrasi e-Commerce | Sinkronisasi Shopee, Tokopedia, Lazada |
| Integrasi Payment Gateway | Midtrans, Xendit, Doku |
| Integrasi e-Faktur DJP | Koneksi langsung ke sistem pajak online |
| Backup & Restore | Pencadangan data otomatis & pemulihan |
| Multi-Branch | Pengelolaan laporan per cabang/outlet |
| Dashboard & KPI | Ringkasan kinerja keuangan real-time |

---

## 14. Prioritas Implementasi

Urutan pengembangan yang direkomendasikan berdasarkan nilai bisnis dan ketergantungan modul:

| Fase | Modul | Cocok untuk | Estimasi |
|------|-------|-------------|----------|
| **Fase 1 — Core** | GL, Kas/Bank, AR, AP | Semua jenis usaha | Sprint 1-3 |
| **Fase 2 — Operasional** | Inventory, Sales, Purchasing | Toko, Cafe, Distributor | Sprint 4-6 |
| **Fase 3 — SDM & Aset** | Payroll, Fixed Assets, Tax | Semua yang berkembang | Sprint 7-9 |
| **Fase 4 — Analitik** | Budgeting, Konsolidasi, BI | Startup skala menengah-besar | Sprint 10-12 |

### Ketergantungan Antar Modul

```
General Ledger ←── semua modul posting ke sini
     ↑
     ├── Kas & Bank
     ├── Piutang (AR) ←── Sales / Invoice
     ├── Hutang (AP)  ←── Purchasing / Invoice
     ├── Inventory    ←── Sales + Purchasing
     ├── Payroll      ──→ Beban Gaji
     ├── Fixed Assets ──→ Beban Penyusutan
     └── Tax          ──→ Hutang Pajak
```

---

## Referensi Standar

- **PSAK** (Pernyataan Standar Akuntansi Keuangan) — standar akuntansi Indonesia
- **IFRS** (International Financial Reporting Standards)
- **Peraturan DJP** — perpajakan Indonesia
- **PMK** (Peraturan Menteri Keuangan) — regulasi fiskal

---

*Dokumen ini merupakan referensi desain fitur SIA untuk sistem ERP internal. Diperbarui sesuai kebutuhan pengembangan.*
