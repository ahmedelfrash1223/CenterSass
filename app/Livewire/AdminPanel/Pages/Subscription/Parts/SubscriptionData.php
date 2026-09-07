<?php

namespace App\Livewire\AdminPanel\Pages\Subscription\Parts;

use App\Models\Subscription;
use Livewire\Component;
use Livewire\WithPagination;

class SubscriptionData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $deleteId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete($id): void
    {
        $this->deleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
    }

    public function delete(): void
    {
        if (! $this->deleteId) {
            return;
        }

        Subscription::findOrFail($this->deleteId)->delete();
        $this->deleteId = null;
        session()->flash('subscription-success', 'تم حذف الاشتراك بنجاح.');
    }

    public function render()
    {
        $subscriptions = Subscription::with(['teacher', 'package'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query->whereHas('teacher', fn ($teacher) => $teacher->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('package', fn ($package) => $package->where('title', 'like', '%' . $this->search . '%'))
                        ->orWhere('status', $this->search);
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin-panel.pages.subscription.parts.subscription-data', compact('subscriptions'));
    }
}
