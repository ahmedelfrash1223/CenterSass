<?php

namespace App\Services\Admin;

use App\Models\CardRequest;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use ZipArchive;

class CardRequestExportService
{
    /**
     * يبني ملف ZIP كامل ويحفظه في storage/app/temp
     *
     * @return array{path:string, filename:string}
     */
    public function buildZip(CardRequest $cardRequest): array
    {
        $cardRequest->load([
            'items.branch',
            'items.group',
            'teacher',
        ]);

        $tempDir = storage_path('app/temp/card-requests/'.$cardRequest->reference.'-'.Str::random(8));
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $teacherName = $this->sanitize($cardRequest->teacher?->name ?? 'teacher');
        $teacherDir = $tempDir.'/'.$teacherName;
        mkdir($teacherDir, 0775, true);

        $itemsByBranchAndGroup = $cardRequest->items->groupBy(function ($item) {
            return $this->sanitize($item->branch?->name ?? 'بدون-فرع');
        });

        foreach ($itemsByBranchAndGroup as $branchName => $items) {
            $branchDir = $teacherDir.'/'.$branchName;
            mkdir($branchDir, 0775, true);

            $itemsByGroup = $items->groupBy(function ($item) {
                return $this->sanitize($item->group?->name ?? 'بدون-مجموعة');
            });

            foreach ($itemsByGroup as $groupName => $groupItems) {
                $groupDir = $branchDir.'/'.$groupName;
                mkdir($groupDir, 0775, true);

                foreach ($groupItems as $item) {
                    $this->saveBarcodePng(
                        $groupDir,
                        $item->student_name,
                        $item->student_code ?? (string) $item->student_id,
                        $item->student_code ?? (string) $item->student_id,
                    );
                }
            }
        }

        $barcodesPdf = $this->buildBarcodesPdf($cardRequest);
        if ($barcodesPdf !== '') {
            file_put_contents($teacherDir.'/بطاقات-الباركود.pdf', $barcodesPdf);
        }

        $checklistPdf = $this->buildChecklistPdf($cardRequest);
        if ($checklistPdf !== '') {
            file_put_contents($teacherDir.'/قائمة-المتابعة.pdf', $checklistPdf);
        }

        $zipName = 'card-request-'.$cardRequest->reference.'.zip';
        $zipPath = $tempDir.'/'.$zipName;

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create ZIP file.');
        }

        $this->addDirToZip($zip, $teacherDir, $teacherName);
        $zip->close();

        return [
            'path' => $zipPath,
            'filename' => $zipName,
        ];
    }

    public function downloadZipResponse(CardRequest $cardRequest)
    {
        $result = $this->buildZip($cardRequest);

        return response()
            ->download($result['path'], $result['filename'], [
                'Content-Type' => 'application/zip',
            ])
            ->deleteFileAfterSend(true);
    }

    // ────────────────────────────────────────
    // Barcode PNG (transparent background)
    // ────────────────────────────────────────
    protected function saveBarcodePng(string $dir, string $studentName, string $code, string $fileBase): void
    {
        $code = trim((string) $code);
        if ($code === '') {
            return;
        }

        $safeName = $this->sanitize($studentName).'-'.$this->sanitize($code);

        try {
            $png = \DNS1D::getBarcodePNG($code, 'C128', 2, 80, [0, 0, 0], true);
            $binary = base64_decode($png);

            if ($binary === '' || $binary === false) {
                throw new \RuntimeException('Empty barcode PNG');
            }

            file_put_contents($dir.'/'.$safeName.'.png', $binary);
        } catch (\Throwable $e) {
            try {
                $svg = \DNS1D::getBarcodeSVG($code, 'C128', 2, 80);
                if (trim($svg) !== '') {
                    file_put_contents($dir.'/'.$safeName.'.svg', $svg);
                }
            } catch (\Throwable $e2) {
                // تجاهل — الطالب هيظهر في الـ PDF بس من غير صورة
            }
        }
    }

    // ────────────────────────────────────────
    // PDF 1: barcodes for printing
    // ────────────────────────────────────────
    protected function buildBarcodesPdf(CardRequest $cardRequest): string
    {
        $items = $cardRequest->items->sortBy([
            fn ($a, $b) => strcmp($a->branch?->name ?? '', $b->branch?->name ?? ''),
            fn ($a, $b) => strcmp($a->group?->name ?? '', $b->group?->name ?? ''),
            fn ($a, $b) => strcmp($a->student_name, $b->student_name),
        ])->values();

        if ($items->isEmpty()) {
            $items = collect();
        }

        $html = view('admin-panel.pages.card-request.pdf.barcodes', [
            'cardRequest' => $cardRequest,
            'items' => $items,
        ])->render();

        if (trim($html) === '') {
            return '';
        }

        $mpdf = $this->makeMpdf();
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', Destination::STRING_RETURN);
    }

    // ────────────────────────────────────────
    // PDF 2: checklist
    // ────────────────────────────────────────
    protected function buildChecklistPdf(CardRequest $cardRequest): string
    {
        $items = $cardRequest->items->sortBy([
            fn ($a, $b) => strcmp($a->group?->name ?? '', $b->group?->name ?? ''),
            fn ($a, $b) => strcmp($a->student_name, $b->student_name),
        ])->values();

        if ($items->isEmpty()) {
            $items = collect();
        }

        $html = view('admin-panel.pages.card-request.pdf.checklist', [
            'cardRequest' => $cardRequest,
            'items' => $items,
        ])->render();

        if (trim($html) === '') {
            return '';
        }

        $mpdf = $this->makeMpdf();
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', Destination::STRING_RETURN);
    }

    // ────────────────────────────────────────
    // mPDF factory
    // ────────────────────────────────────────
    protected function makeMpdf(): Mpdf
    {
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
                Classio — نظام إدارة المراكز التعليمية | '.now()->format('Y-m-d H:i').'
            </div>
        ');

        return $mpdf;
    }

    // ────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────
    protected function addDirToZip(ZipArchive $zip, string $dir, string $zipPrefix): void
    {
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $full = $dir.'/'.$item;
            $local = $zipPrefix.'/'.$item;

            if (is_dir($full)) {
                $zip->addEmptyDir($local);
                $this->addDirToZip($zip, $full, $local);
            } else {
                $zip->addFile($full, $local);
            }
        }
    }

    protected function sanitize(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[\/\\\\:*?"<>|]+/u', '-', $value);
        $value = preg_replace('/\s+/u', '-', $value);

        return $value !== '' ? $value : 'unknown';
    }
}
