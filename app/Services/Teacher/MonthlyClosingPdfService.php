<?php

namespace App\Services\Teacher;

use App\Models\MonthlyClosing;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class MonthlyClosingPdfService
{
    public function revenuePdf(MonthlyClosing $closing): string
    {
        $closing->load(['branch', 'teacher', 'closedBy']);

        $items = $closing->items()
            ->where('type', 'revenue')
            ->orderBy('occurred_at')
            ->get();

        $html = view('teacher-panel.pages.monthly-closing.pdf.revenue', [
            'closing' => $closing,
            'items' => $items,
        ])->render();

        return $this->render($html);
    }

    public function expensePdf(MonthlyClosing $closing): string
    {
        $closing->load(['branch', 'teacher', 'closedBy']);

        $items = $closing->items()
            ->where('type', 'expense')
            ->orderBy('occurred_at')
            ->get();

        $html = view('teacher-panel.pages.monthly-closing.pdf.expense', [
            'closing' => $closing,
            'items' => $items,
        ])->render();

        return $this->render($html);
    }

    public function summaryPdf(MonthlyClosing $closing): string
    {
        $closing->load(['branch', 'teacher', 'closedBy', 'items']);

        $html = view('teacher-panel.pages.monthly-closing.pdf.summary', [
            'closing' => $closing,
        ])->render();

        return $this->render($html);
    }

    protected function render(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 15,
            'margin_bottom' => 20,
            'margin_header' => 5,
            'margin_footer' => 10,
            'default_font' => 'dejavusans',
            'default_font_size' => 10,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'tempDir' => storage_path('app/mpdf'),
            'shrink_tables_to_fit' => 1,
            'keep_table_proportions' => true,
            'autoPadding' => true,
        ]);

        $mpdf->SetDirectionality('rtl');
        $mpdf->SetHTMLFooter('
            <div style="text-align:center; font-size:9px; color:#999; border-top:1px solid #ddd; padding-top:5px;">
                نبذة — نظام إدارة المراكز التعليمية | '.now()->format('Y-m-d H:i').'
            </div>
        ');

        $mpdf->WriteHTML($html);

        return $mpdf->Output('', Destination::STRING_RETURN);
    }

    public function downloadResponse(string $pdfBinary, string $filename)
    {
        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function streamResponse(string $pdfBinary, string $filename)
    {
        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }
}
