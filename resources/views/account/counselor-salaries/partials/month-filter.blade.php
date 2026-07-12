<div class="d-flex flex-wrap gap-2 align-items-center">
    @foreach($availableMonths as $month)
        <a href="{{ $monthUrl($month['value']) }}"
           class="btn btn-sm {{ $selectedMonth === $month['value'] ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $month['label'] }}
        </a>
    @endforeach
    <form method="GET" action="{{ $filterAction }}" class="d-flex gap-2 align-items-center ms-auto">
        @foreach($hiddenFields ?? [] as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach
        <input type="month" name="month" class="form-control form-control-sm" value="{{ $selectedMonth }}"
            min="{{ $minMonth }}" max="{{ $maxMonth }}" style="width: auto;">
        <button type="submit" class="btn btn-sm btn-primary">Go</button>
    </form>
</div>
