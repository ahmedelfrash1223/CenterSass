<?php

namespace App\Http\Controllers\AdminPanel;

use App\Enums\Teacher\CardRequestStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\CardRequest;
use App\Services\Admin\CardRequestExportService;

class CardRequestExportController extends Controller
{
    public function __construct(protected CardRequestExportService $exportService) {}

    public function downloadZip(CardRequest $cardRequest)
    {
        // لا صلاحيات — الأدمن فقط
        // لكن نمنع التصدير قبل الموافقة/البدء
        if (! in_array($cardRequest->status, [
            CardRequestStatusEnum::APPROVED,
            CardRequestStatusEnum::IN_PROGRESS,
            CardRequestStatusEnum::COMPLETED,
            CardRequestStatusEnum::SHIPPED,
            CardRequestStatusEnum::DELIVERED,
        ], true)) {
            abort(403, 'لا يمكن تصدير هذا الطلب في حالته الحالية.');
        }

        return $this->exportService->downloadZipResponse($cardRequest);
    }
}
