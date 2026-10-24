<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Models\MonthlyClosing;
use App\Services\Teacher\MonthlyClosingPdfService;
use Illuminate\Support\Facades\Auth;

class MonthlyClosingController extends Controller
{
    public function index()
    {
        return view('teacher-panel.pages.monthly-closing.index');
    }

    public function revenuePdf(MonthlyClosing $closing, MonthlyClosingPdfService $pdf)
    {
        $this->authorizeOwnership($closing);

        $binary = $pdf->revenuePdf($closing);
        return $pdf->streamResponse($binary, "revenue-{$closing->reference}.pdf");
    }

    public function expensePdf(MonthlyClosing $closing, MonthlyClosingPdfService $pdf)
    {
        $this->authorizeOwnership($closing);

        $binary = $pdf->expensePdf($closing);
        return $pdf->streamResponse($binary, "expense-{$closing->reference}.pdf");
    }

    public function summaryPdf(MonthlyClosing $closing, MonthlyClosingPdfService $pdf)
    {
        $this->authorizeOwnership($closing);

        $binary = $pdf->summaryPdf($closing);
        return $pdf->streamResponse($binary, "summary-{$closing->reference}.pdf");
    }

    protected function authorizeOwnership(MonthlyClosing $closing): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user && $closing->teacher_id === $user->getOwnerId(), 404);
    }
}
