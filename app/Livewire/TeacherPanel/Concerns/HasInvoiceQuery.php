<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait HasInvoiceQuery
{

    protected function baseInvoiceQuery(): Builder
    {
        $user = $this->currentUser();
        $ownerId = $user->getOwnerId();
        $branchIds = $user->getAccessibleBranchIds();

        $query = Invoice::query()
            ->where('teacher_id', $ownerId)
            ->whereNull('cancelled_at');

        if (!$user->isOwner()) {
            empty($branchIds)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('branch_id', $branchIds);
        }

        return $query;
    }


    protected function applyMonthFilter(Builder $query): Builder
    {
        if ($this->monthFilter === '') {
            return $query;
        }

        try {
            $date = Carbon::createFromFormat('Y-m', $this->monthFilter);

            return $query
                ->whereDate('period_start', $date->copy()->startOfMonth()->toDateString())
                ->whereDate('period_end', $date->copy()->endOfMonth()->toDateString());
        } catch (\Exception $e) {
            return $query;
        }
    }

    protected function applySearchFilter(Builder $query, string $search): Builder
    {
        if ($search === '') {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'like', "%{$search}%")
                ->orWhereHas('student', function ($s) use ($search) {
                    $s->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
        });
    }


    protected function applyStatusFilter(Builder $query, string $status): Builder
    {
        if ($status === '') {
            return $query;
        }

        return $query->where('status', $status);
    }
}
