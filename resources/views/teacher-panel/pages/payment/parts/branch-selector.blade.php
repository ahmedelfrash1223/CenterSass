<select wire:model.live="branchId" class="form-select">
    <option value="">كل الفروع</option>
    @foreach($branches as $branch)
        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
    @endforeach
</select>
