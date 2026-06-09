<?php

namespace App\Http\Controllers;

use App\Models\SalesQuotation;
use App\Helpers\Terbilang;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesQuotationPdfController extends Controller
{
    public function download($id)
    {
        $quotation = SalesQuotation::with(['customer', 'items.product'])->findOrFail($id);

        // Calculate grand total
        $grandTotal = 0;
        foreach ($quotation->items as $item) {
            $grandTotal += ($item->qty * $item->price) - ($item->discount ?? 0);
        }

        $company = [
            'name' => config('app.company_name', 'CV. Radi Amartha Mahardika'),
            'address' => config('app.company_address', 'JL. E-II No, C1, Komp. BAKN, Kelurahan Sumur Batu, Kecamatan Kemayoran, Jakarta Pusat, DKI Jakarta'),
            'phone' => config('app.company_phone', '+62-888-0868-6293'),
            'email' => config('app.company_email', 'marketing@radiamartha.com'),
            'website' => config('app.company_website', 'radiamartha.com'),
            'bank_name' => config('app.company_bank_name', 'BCA'),
            'bank_account' => config('app.company_bank_account', '6250887700'),
            'bank_holder' => config('app.company_bank_holder', 'Muhammad Rizky Ramadhan'),
        ];

        $terbilang = Terbilang::rupiah($grandTotal);

        $pdf = Pdf::loadView('pdf.sales-quotation', [
            'quotation' => $quotation,
            'company' => $company,
            'terbilang' => $terbilang,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Proforma-Invoice-' . $quotation->sq_number . '.pdf');
    }

    public function stream($id)
    {
        $quotation = SalesQuotation::with(['customer', 'items.product'])->findOrFail($id);

        $grandTotal = 0;
        foreach ($quotation->items as $item) {
            $grandTotal += ($item->qty * $item->price) - ($item->discount ?? 0);
        }

        $company = [
            'name' => config('app.company_name', 'CV. Radi Amartha Mahardika'),
            'address' => config('app.company_address', 'JL. E-II No, C1, Komp. BAKN, Kelurahan Sumur Batu, Kecamatan Kemayoran, Jakarta Pusat, DKI Jakarta'),
            'phone' => config('app.company_phone', '+62-888-0868-6293'),
            'email' => config('app.company_email', 'marketing@radiamartha.com'),
            'website' => config('app.company_website', 'radiamartha.com'),
            'bank_name' => config('app.company_bank_name', 'BCA'),
            'bank_account' => config('app.company_bank_account', '6250887700'),
            'bank_holder' => config('app.company_bank_holder', 'Muhammad Rizky Ramadhan'),
        ];

        $terbilang = Terbilang::rupiah($grandTotal);

        $pdf = Pdf::loadView('pdf.sales-quotation', [
            'quotation' => $quotation,
            'company' => $company,
            'terbilang' => $terbilang,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Proforma-Invoice-' . $quotation->sq_number . '.pdf');
    }
}
