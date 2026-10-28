<?php

namespace App\Http\Controllers;

use App\Models\SalesQuotation;
use App\Helpers\Terbilang;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesQuotationPdfController extends Controller
{
    private function getPdfData($id): array
    {
        $quotation = SalesQuotation::with(['customer', 'items.product'])->findOrFail($id);

        $calcItemsSubtotal = $quotation->calculated_items_subtotal;

        $grandTotal = floatval($quotation->grand_total ?: 0);
        if ($grandTotal <= 0) {
            $grandTotal = $calcItemsSubtotal;
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

        return [
            'quotation' => $quotation,
            'calcItemsSubtotal' => $calcItemsSubtotal,
            'taxLabel' => $quotation->tax_label,
            'company' => $company,
            'terbilang' => $terbilang,
        ];
    }

    public function download($id)
    {
        $data = $this->getPdfData($id);

        $pdf = Pdf::loadView('pdf.sales-quotation', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Proforma-Invoice-' . $data['quotation']->sq_number . '.pdf');
    }

    public function stream($id)
    {
        $data = $this->getPdfData($id);

        $pdf = Pdf::loadView('pdf.sales-quotation', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Proforma-Invoice-' . str_replace(['/', '\\'], '-', $data['quotation']->sq_number) . '.pdf';

        return $pdf->stream($filename);
    }
}
