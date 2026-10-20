<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Proforma Invoice - {{ $sale->invoice_number }}</title>
    <style>
        @page {
            margin: 30px 40px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10.5px;
            color: #000;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        /* === HEADER === */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        .logo-img {
            height: 150px;
            width: auto;
            vertical-align: middle;
        }

        .company-header-title {
            font-size: 15px;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 3px 0;
        }

        .company-tagline {
            font-size: 10px;
            font-style: italic;
            color: #222;
            margin: 0;
        }

        /* === TITLE === */
        .doc-title {
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            color: #000;
            margin-top: 15px;
            margin-bottom: 15px;
            letter-spacing: 0.5px;
        }

        /* === METADATA === */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            line-height: 1.45;
            margin-bottom: 12px;
        }

        .meta-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .meta-label {
            width: 70px;
        }

        .meta-sep {
            width: 15px;
        }

        /* === ITEMS TABLE === */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
        }

        .items-table th,
        .items-table td {
            border: 1.5px solid #000;
            padding: 4px 5px;
        }

        .items-table th {
            text-align: center;
            font-weight: bold;
            background-color: #fff;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .font-bold {
            font-weight: bold;
        }

        /* === INFO BOX (TERBILANG & BANK) === */
        .info-box {
            border: 1.5px solid #000;
            border-top: none;
            padding: 8px 10px;
            font-size: 9.5px;
            line-height: 1.45;
        }

        /* === SIGNATURE SECTION === */
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        .signature-table td {
            vertical-align: top;
        }

        .signature-block {
            text-align: center;
            font-size: 10px;
            line-height: 1.35;
        }

        .signature-space {
            height: 55px;
        }

        .director-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* === BOTTOM FOOTER === */
        .bottom-footer {
            margin-top: 25px;
            font-size: 9px;
            line-height: 1.4;
            color: #000;
        }

        .footer-company-name {
            font-weight: bold;
            font-size: 9.5px;
        }
    </style>
</head>

<body>

    <!-- HEADER: LOGO + COMPANY NAME + TAGLINE -->
    <table class="header-table">
        <tr>
            <td style="width: 125px; vertical-align: middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo">
                @endif
            </td>
            <td style="padding-left: 12px; vertical-align: middle;">
                <div class="company-header-title">{{ $company['name'] }}</div>
                <div class="company-tagline">{{ $company['tagline'] }}</div>
            </td>
        </tr>
    </table>

    <!-- DOCUMENT TITLE -->
    <div class="doc-title">PROFORMA INVOICE</div>

    <!-- METADATA & RECIPIENT -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Nomor</td>
            <td class="meta-sep">:</td>
            <td>{{ $sale->invoice_number }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal</td>
            <td class="meta-sep">:</td>
            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->locale('id')->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top: 10px; padding-bottom: 2px;">Kepada Yth.</td>
        </tr>
        <tr>
            <td class="meta-label">Nama</td>
            <td class="meta-sep">:</td>
            <td>
                {{ $sale->customer->name ?? ($sale->customer_name ?? '-') }}
                @if(!empty($sale->customer->company_name) && ($sale->customer->type ?? '') === 'company')
                    - {{ $sale->customer->company_name }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="meta-label">Alamat</td>
            <td class="meta-sep">:</td>
            <td>{{ $sale->customer->address ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Email</td>
            <td class="meta-sep">:</td>
            <td>{{ $sale->customer->email }}</td>
        </tr>
        <tr>
            <td class="meta-label">No. Hp</td>
            <td class="meta-sep">:</td>
            <td>{{ $sale->customer->phone ?? '-' }}</td>
        </tr>
    </table>

    <!-- ITEMS TABLE -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 37%;">Product</th>
                <th style="width: 16%;">Model</th>
                <th style="width: 16%;">Harga/unit</th>
                <th style="width: 8%;">QTY</th>
                <th style="width: 18%;">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $i => $item)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-left">{{ $item->product_name ?: ($item->product->name ?? '-') }}</td>
                    <td class="text-center">{{ $item->model ?: ($item->product->sku ?: ($item->product->unit ?: '-')) }}
                    </td>
                    <td class="text-right">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ (int) $item->quantity }}</td>
                    <td class="text-right">Rp{{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-center font-bold">Shipping Price</td>
                <td class="text-right">Rp{{ number_format($sale->shipping_price, 0, ',', '.')  }}</td>
            </tr>
            <tr>
                <td colspan="5" class="text-center font-bold">Subtotal (DPP)</td>
                <td class="text-right">Rp{{ number_format($sale->total_amount, 0, ',', '.') }}</td>
            </tr>

            @if(($sale->discount_amount ?? 0) > 0)
                <tr>
                    <td colspan="5" class="text-center font-bold">Diskon</td>
                    <td class="text-right">- Rp{{ number_format($sale->discount_amount, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="5" class="text-center font-bold">{{ $taxLabel }}</td>
                <td class="text-right">Rp{{ number_format($sale->tax_amount, 0, ',', '.') }}</td>
            </tr>

            @if(($sale->shipping_price ?? 0) > 0)
                <tr>
                    <td colspan="5" class="text-center font-bold">Shipping Price</td>
                    <td class="text-right">Rp{{ number_format($sale->shipping_price, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="5" class="text-center font-bold">Total Harga</td>
                <td class="text-right font-bold">Rp{{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    <div class="info-box">
        <div style="font-weight: bold; margin-bottom: 8px;">{{ $terbilang }}</div>
        <div style="font-weight: bold; margin-top: 4px;">Informasi Bank:</div>
        <div style="font-weight: bold;">{{ $company['bank_info'] }}</div>
        <div style="font-weight: bold;">Atas Nama: {{ $company['bank_holder'] }}</div>
    </div>
    <table class="signature-table">
        <tr>
            <td style="width: 55%;"></td>
            <td style="width: 45%;">
                <div class="signature-block">
                    <div>CV Radi Amartha Mahardika</div>
                    <div class="signature-space"></div>
                    <div class="director-name">{{ $company['director_name'] }}</div>
                    <div>{{ $company['director_title'] }}</div>
                </div>
            </td>
        </tr>
    </table>
    <div class="bottom-footer">
        <div class="footer-company-name">{{ $company['name'] }}</div>
        <div style="margin-top: 3px;">{{ $company['address'] }}</div>
        <div style="margin-top: 2px;">Telp: {{ $company['phone'] }} | Email: {{ $company['email'] }} |</div>
        <div style="margin-top: 2px;">Web: {{ $company['website'] }}</div>
    </div>

</body>

</html>