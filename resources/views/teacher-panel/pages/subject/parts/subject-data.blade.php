<div>
    <!-- List Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><p class="text-muted mb-0 small">إدارة المراحل والمواد الدراسية التي تقوم بتدريسها</p></div>
        <a href="{{ route('teacher.subjects.create') }}" wire:navigate
           class="btn btn-primary d-flex align-items-center gap-1"><i class="ph-duotone ph-plus"></i>إضافة مادة</a>
    </div>

    <!-- Feedback -->
    @if (session('subject-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4"><i
                class="ph-duotone ph-check-circle fs-5"></i><span>{{ session('subject-success') }}</span></div>
    @endif

    <!-- Subject Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom">
            <div class="position-relative col-md-6"><i
                    class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i><input
                    type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5"
                    placeholder="ابحث عن مرحلة أو مادة..."></div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>المرحلة</th>
                    <th>المادة</th>
                    <th>الحالة</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($subjects as $item)
                    @php($gradeColor = str_starts_with($item->grade, 'primary_') ? 'success' : (str_starts_with($item->grade, 'prep_') ? 'primary' : 'info'))
                    <tr wire:key="teacher-subject-{{ $item->id }}">
                        <td class="ps-4">
                            <span class="text-muted fw-semibold">{{ $loop->iteration }}</span>
                        </td>                        <td><span
                                class="badge bg-{{ $gradeColor }} bg-opacity-10 text-{{ $gradeColor }}">{{ $grades[$item->grade] ?? $item->grade }}</span>
                        </td>
                        <td>
                            <i class="ph-duotone ph-books me-1 text-primary"></i>{{ $subjectOptions[$item->subject] ?? $item->subject }}
                        </td>
                        <td>@if($item->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success"><i
                                        class="ph-duotone ph-check-circle me-1"></i>نشط</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger"><i
                                        class="ph-duotone ph-x-circle me-1"></i>غير نشط</span>
                            @endif</td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                @if($deleteId === $item->id)
                                    <button type="button" wire:click="delete" wire:loading.attr="disabled"
                                            wire:target="delete" class="border-0 bg-transparent p-0 text-success"
                                            title="تأكيد الحذف"><span wire:loading.remove wire:target="delete"><i
                                                class="ph-duotone ph-check-circle fs-4"></i></span><span wire:loading
                                                                                                         wire:target="delete"
                                                                                                         class="spinner-border spinner-border-sm"></span>
                                    </button>
                                    <button type="button" wire:click="cancelDelete"
                                            class="border-0 bg-transparent p-0 text-secondary" title="إلغاء"><i
                                            class="ph-duotone ph-x-circle fs-4"></i></button>
                                @else
                                    <a href="{{ route('teacher.subjects.edit', $item) }}" wire:navigate
                                       class="border-0 bg-transparent p-0 text-primary" title="تعديل"><i
                                            class="ph-duotone ph-pencil-simple fs-4"></i></a>
                                    <button type="button" wire:click="confirmDelete({{ $item->id }})"
                                            class="border-0 bg-transparent p-0 text-danger" title="حذف"><i
                                            class="ph-duotone ph-trash fs-4"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted"><i class="ph-duotone ph-books fs-1 d-block mb-2"></i>
                                <h6>{{ $search ? 'لا توجد نتائج' : 'لا توجد مواد مضافة' }}</h6>
                                <small>{{ $search ? 'لم يتم العثور على مادة مطابقة.' : 'ابدأ بإضافة أول مادة دراسية.' }}</small>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($subjects->hasPages())
            <div class="card-footer bg-transparent border-top">{{ $subjects->links() }}</div>
        @endif
    </div>
</div>
