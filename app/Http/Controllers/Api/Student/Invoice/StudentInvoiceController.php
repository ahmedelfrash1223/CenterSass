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

        return ApiResponse::paginated(
            paginator: $paginator,
            resourceClass: StudentInvoiceResource::class,
            message: 'سجل الفواتير.',
        );
    }


    public function stats(ListInvoiceRequest $request): JsonResponse
    {
        $account = auth('student_account')->user();

        $stats = $this->invoiceService->statsForAccount(
            $account,
            $request->input('month')
        );

        return ApiResponse::success(
            data: $stats,
            message: 'إحصائيات الفواتير.',
            extra: ['month' => $request->input('month', now()->format('Y-m'))],
        );
    }
}
