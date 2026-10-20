<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Helpers\Terbilang;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SalePdfController extends Controller
{
    public function download($id)
    {
        $sale = Sale::with(['customer', 'items.product'])->findOrFail($id);
        $pdf = $this->generatePdf($sale);
        $filename = 'Proforma-Invoice-' . str_replace(['/', '\\'], '-', $sale->invoice_number) . '.pdf';

        return $pdf->download($filename);
    }

    public function stream($id)
    {
        $sale = Sale::with(['customer', 'items.product'])->findOrFail($id);
        $pdf = $this->generatePdf($sale);
        $filename = 'Proforma-Invoice-' . str_replace(['/', '\\'], '-', $sale->invoice_number) . '.pdf';

        return $pdf->stream($filename);
    }

    private function generatePdf(Sale $sale)
    {
        Carbon::setLocale('id');

        $company = [
            'name' => config('app.company_name', 'CV. RADI AMARTHA MAHARDIKA'),
            'tagline' => '"Your Trusted Industrial & Lab Equipment Partner"',
            'address' => config('app.company_address', 'Jalan E2 No. C1, Komp. BAKN, Sumur Batu, Kemayoran, Jakarta Pusat'),
            'phone' => config('app.company_phone', '+62-888-0868-6293'),
            'email' => config('app.company_email', 'Marketing@radiamartha.com'),
            'website' => config('app.company_website', 'Radiamartha.com'),
            'bank_info' => config('app.company_bank_info', 'BLU By BCA Digital — 090190400040'),
            'bank_holder' => config('app.company_bank_holder', 'MUHAMMAD RIZKY RAMADHAN'),
            'director_name' => config('app.company_director', 'Rediana Putri Nurrohma, S. Biotek'),
            'director_title' => config('app.company_director_title', 'Direktur'),
        ];

        // Logo in base64 to ensure DomPDF renders it properly
        $logoPath = public_path('assets/logo.png');
        $logoBase64 = null;
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Terbilang calculation
        $terbilang = trim(Terbilang::convert($sale->grand_total));

        // Tax label determination (e.g. PPh 0,5%, PPN 12%, Bebas Pajak 0%)
        if ($sale->tax_type === 'pph_05') {
            $taxLabel = 'PPh 0,5%';
        } elseif ($sale->tax_type === 'ppn_12' || $sale->tax_type === 'ppn_11') {
            $taxLabel = 'PPN 12%';
        } elseif ($sale->tax_type === 'none') {
            $taxLabel = 'Bebas Pajak (0%)';
        } else {
            $taxRate = $sale->total_amount > 0 ? round(($sale->tax_amount / $sale->total_amount) * 100, 1) : 0;
            if ($taxRate == 0.5 || ($sale->tax_amount > 0 && $taxRate < 1)) {
                $taxLabel = 'PPh 0,5%';
            } elseif ($taxRate > 0) {
                $taxLabel = 'PPN ' . ($taxRate == (int)$taxRate ? (int)$taxRate : $taxRate) . '%';
            } else {
                $taxLabel = 'PPh 0,5%';
            }
        }

        $pdf = Pdf::loadView('pdf.sale', [
            'sale' => $sale,
            'company' => $company,
            'logoBase64' => $logoBase64,
            'terbilang' => $terbilang,
            'taxLabel' => $taxLabel,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }
}
