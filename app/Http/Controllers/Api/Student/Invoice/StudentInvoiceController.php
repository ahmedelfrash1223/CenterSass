<?php

namespace App\Http\Controllers\Api\Student\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\Invoice\ListInvoiceRequest;
use App\Http\Resources\Api\Student\Invoice\StudentInvoiceResource;
use App\Services\Api\Student\Invoice\StudentInvoiceService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class StudentInvoiceController extends Controller
{
    public function __construct(
        private readonly StudentInvoiceService $invoiceService,
    ) {}

    public function index(ListInvoiceRequest $request): JsonResponse
    {
        $account = auth('student_account')->user();

        $paginator = $this->invoiceService->listForAccount($account, $request->validated());

        $stats = $this->invoiceService->statsForAccount(
            $account,
            $request->input('month') ?: now()->format('Y-m')
        );

        return ApiResponse::paginated(
            paginator: $paginator,
            resourceClass: StudentInvoiceResource::class,
            message: 'سجل الفواتير.',
            extra: ['stats' => $stats]
        );
    }

    public function stats(ListInvoiceRequest $request): JsonResponse
    {
        $account = auth('student_account')->user();
        $month = $request->input('month') ?: now()->format('Y-m');
        $stats = $this->invoiceService->statsForAccount($account, $month);

        return ApiResponse::success(
            data: $stats,
            message: 'إحصائيات الفواتير.',
            extra: ['month' => $month],
        );
    }
}
