<?php

namespace App\Services\Teacher;

use App\Models\Invoice;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;

class PaymentQueryService
{
    /**
     * Search students for the payment screen.
     * Supports barcode/uuid digits + fuzzy matching.
     */
    public function searchStudents(Teacher $user, string $query): Builder
    {
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $builder = Student::query()
            ->where('teacher_id', $ownerId)
            ->with(['branch', 'activeGroups'])
            ->withCount(['activeGroups as groups_count']);

        // Non-owner: restrict to accessible branches
        if (!$user->isOwner()) {
            $builder->whereIn('branch_id', $accessibleBranches);
        }

        $query = trim($query);

        if ($query === '') {
            return $builder->orderBy('name');
        }

        $upper = strtoupper($query);
        $lower = strtolower($query);
        $digits = preg_replace('/\D/', '', $query);

        $builder->where(function ($q) use ($query, $upper, $lower, $digits) {
            // Name / username / email / phone
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('username', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orWhere('code', $upper)
                ->orWhere('code', $lower)
                ->orWhere('uuid', $lower);

            // Barcode digits
            if (strlen($digits) >= 4) {
                $q->orWhere('barcode_digits', $digits)
                    ->orWhere('barcode_digits', 'like', "%{$digits}%");
            }
        });

        return $builder->orderBy('name');
    }

    /**
     * Invoices for a specific student, scoped to the user.
     */
    public function studentInvoices(Teacher $user, int $studentId): Builder
    {
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $builder = Invoice::query()
            ->where('teacher_id', $ownerId)
            ->where('student_id', $studentId)
            ->with(['items.group', 'payments'])
            ->orderByDesc('period_start');

        if (!$user->isOwner()) {
            $builder->whereIn('branch_id', $accessibleBranches);
        }

        return $builder;
    }
}
