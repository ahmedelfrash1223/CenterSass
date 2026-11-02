<section class="nb-card nb-card--flush nb-home__branches" aria-labelledby="nb-branches-title">
    <header class="nb-card__head">
        <div class="nb-card__heading">
            <span class="nb-icon-tile nb-icon-tile--sm" aria-hidden="true"><i class="ph-duotone ph-buildings"></i></span>
            <h3 class="nb-card__title" id="nb-branches-title">إحصائيات الفروع</h3>
        </div>
        <a href="{{ route('teacher.branches.index') }}" wire:navigate class="nb-card__link">
            عرض الكل
            <i class="ph-duotone ph-caret-left" aria-hidden="true"></i>
        </a>
    </header>

    <div class="table-responsive">
        <table class="table align-middle mb-0 nb-home__table">
            <thead>
            <tr>
                <th scope="col">الفرع</th>
                <th scope="col" class="text-center">الطلاب</th>
                <th scope="col" class="text-center">المجموعات</th>
                <th scope="col" class="text-center">حصص النهارده</th>
                <th scope="col" class="text-center">الحالة</th>
            </tr>
            </thead>
            <tbody>
            @forelse($branches as $branch)
                <tr wire:key="branch-{{ $branch->id }}">
                    <td>
                        <div class="nb-home__branch">
                            <span class="nb-icon-tile nb-icon-tile--sm" aria-hidden="true"><i class="ph-duotone ph-map-pin"></i></span>
                            <span class="nb-home__list-text">
                                <strong>{{ $branch->name }}</strong>
                                @if($branch->phone)
                                    <small dir="ltr">{{ $branch->phone }}</small>
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="text-center nb-home__num">{{ $branch->students_count }}</td>
                    <td class="text-center nb-home__num">{{ $branch->groups_count }}</td>
                    <td class="text-center">
                        @if($branch->today_schedules_count > 0)
                            <span class="nb-pill">{{ $branch->today_schedules_count }} حصة</span>
                        @else
                            <span class="nb-home__muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($branch->is_active)
                            <span class="nb-pill nb-pill--success">نشط</span>
                        @else
                            <span class="nb-pill nb-pill--danger">غير نشط</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="nb-empty">
                            <span class="nb-empty__icon" aria-hidden="true"><i class="ph-duotone ph-map-pin"></i></span>
                            <p class="nb-empty__text">لا توجد فروع</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($branches->hasPages())
        <div class="nb-home__pagination">{{ $branches->links() }}</div>
    @endif
</section>
