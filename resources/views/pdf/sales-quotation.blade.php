<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Proforma Invoice - {{ $quotation->sq_number }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #222;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* === HEADER === */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0;
        }
        .company-contact {
            font-size: 9px;
            color: #444;
            margin-top: 2px;
        }
        .doc-title {
            font-size: 22px;
            font-weight: bold;
            text-align: right;
            color: #1a1a1a;
            padding-top: 5px;
        }

        .divider {
            border-top: 2px solid #333;
            margin: 8px 0;
        }

        /* === INFO SECTION === */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #333;
            width: 120px;
        }
        .info-section-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 3px;
            color: #111;
            text-decoration: underline;
        }

        /* === META (No. Invoice, Tanggal) === */
        .meta-box {
            border: 1px solid #999;
            border-collapse: collapse;
            width: 100%;
            font-size: 10px;
        }
        .meta-box td, .meta-box th {
            border: 1px solid #999;
            padding: 4px 8px;
        }
        .meta-box th {
            background: #f0f0f0;
            text-align: left;
            font-weight: bold;
        }

        /* === ITEMS TABLE === */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 10px;
        }
        .items-table thead th {
            background: #f5f5f5;
            border: 1px solid #999;
            padding: 6px 8px;
            font-weight: bold;
            text-align: center;
        }
        .items-table tbody td {
            border: 1px solid #999;
            padding: 5px 8px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }

        /* === SUMMARY === */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .summary-table td {
            padding: 3px 8px;
        }
        .summary-border td {
            border: 1px solid #999;
        }
        .summary-label {
            text-align: right;
            font-weight: bold;
            background: #f5f5f5;
        }
        .summary-value {
            text-align: right;
            font-weight: bold;
        }

        /* === FOOTER === */
        .footer-section {
            margin-top: 15px;
            font-size: 10px;
        }
        .terbilang {
            font-style: italic;
            font-weight: bold;
            margin: 10px 0;
            padding: 6px 10px;
            border: 1px solid #ccc;
            background: #fafafa;
        }
        .bank-info {
            margin-top: 10px;
        }
        .bank-info strong {
            display: block;
            margin-bottom: 3px;
        }

        /* === SIGNATURES === */
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-table td {
            text-align: center;
            padding-top: 60px;
            width: 33%;
            font-size: 10px;
        }
        .signature-line {
            border-top: 1px solid #333;
            display: inline-block;
            width: 150px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="company-name">{{ $company['name'] }}</div>
                <div class="company-contact">
                    {{ $company['address'] }}<br>
                    Telp: {{ $company['phone'] }} &nbsp; E-mail: {{ $company['email'] }}<br>
                    @if(!empty($company['website']))
                        Website: {{ $company['website'] }}
                    @endif
                </div>
            </td>
            <td style="width: 40%;">
                <div class="doc-title">PROFORMA INVOICE</div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- COMPANY & CUSTOMER INFO -->
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 55%; vertical-align: top; padding-right: 15px;">
                <div class="info-section-title">Penerbit:</div>
                <table class="info-table">
                    <tr><td>{{ $company['name'] }}</td></tr>
                    <tr><td>{{ $company['address'] }}</td></tr>
                    <tr><td>Telp: {{ $company['phone'] }}</td></tr>
                    <tr><td>Email: {{ $company['email'] }}</td></tr>
                </table>

                <div class="info-section-title" style="margin-top: 8px;">Penerima:</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Nama / Perusahaan:</td>
                        <td>{{ $quotation->customer->name }} @if($quotation->customer->company_name) - {{ $quotation->customer->company_name }} @endif</td>
                    </tr>
                    @if($quotation->customer->phone)
                    <tr>
                        <td class="info-label">Telepon:</td>
                        <td>{{ $quotation->customer->phone }}</td>
                    </tr>
                    @endif
                    @if($quotation->customer->email)
                    <tr>
                        <td class="info-label">Email:</td>
                        <td>{{ $quotation->customer->email }}</td>
                    </tr>
                    @endif
                    @if($quotation->customer->address)
                    <tr>
                        <td class="info-label">Alamat:</td>
                        <td>{{ $quotation->customer->address }}</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <table class="meta-box">
                    <tr>
                        <th>No. Invoice:</th>
                        <th>Tanggal:</th>
                    </tr>
                    <tr>
                        <td>{{ $quotation->sq_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($quotation->created_at)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Berlaku Sampai:</strong> {{ \Carbon\Carbon::parse($quotation->valid_until)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Skema Pembayaran:</strong> 100% di muka</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ITEMS TABLE -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 45%;">Nama Produk</th>
                <th style="width: 17%;">Harga/Unit</th>
                <th style="width: 8%;">QTY</th>
                <th style="width: 10%;">Diskon</th>
                <th style="width: 15%;">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; $totalDiscount = 0; @endphp
            @foreach($quotation->items as $i => $item)
                @php
                    $lineTotal = $item->qty * $item->price;
                    $disc = $item->discount ?? 0;
                    $subtotal = $lineTotal - $disc;
                    $grandTotal += $subtotal;
                    $totalDiscount += $disc;
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ (int)$item->qty }}</td>
                    <td class="text-right">Rp {{ number_format($disc, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTALS -->
    <table class="summary-table" style="margin-top: 0;">
        <tr class="summary-border">
            <td style="width: 75%;" colspan="4"></td>
            <td class="summary-label" style="width: 10%;">Diskon</td>
            <td class="summary-value" style="width: 15%;">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</td>
        </tr>
        <tr class="summary-border">
            <td colspan="4"></td>
            <td class="summary-label" style="font-size: 12px;">Total Harga</td>
            <td class="summary-value" style="font-size: 12px;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- TERBILANG -->
    <div class="footer-section">
        <div class="terbilang">
            <strong>Terbilang:</strong> {{ $terbilang }}
        </div>

        <!-- BANK INFO -->
        <div class="bank-info">
            <strong>Informasi Bank:</strong>
            {{ $company['bank_name'] ?? 'BCA' }} - {{ $company['bank_account'] ?? '-' }}<br>
            Atas Nama: {{ $company['bank_holder'] ?? $company['name'] }}
        </div>
    </div>

    <!-- SIGNATURE -->
    <table class="signature-table">
        <tr>
            <td>
                <div>Hormat Kami,</div>
                <br><br><br>
                <div class="signature-line"></div>
                <div>{{ $company['name'] }}</div>
            </td>
            <td></td>
            <td>
                <div>Penerima,</div>
                <br><br><br>
                <div class="signature-line"></div>
                <div>{{ $quotation->customer->name }}</div>
            </td>
        </tr>
    </table>

</body>
</html>
