@extends('account.layouts.portal')
@section('title', 'Counselor Salaries')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @include('account.partials.alerts')

    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                <h5 class="mb-0">Counselor Salaries — {{ $monthLabel }}</h5>
            </div>
            @include('account.counselor-salaries.partials.month-filter', [
                'filterAction' => account_route('counselor-salaries.index'),
                'monthUrl' => fn ($month) => account_route('counselor-salaries.index', ['month' => $month]),
            ])
        </div>
        <div class="card-body">           
            <div class="table-responsive">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            <th>Counselor</th>
                            <th>Joining Date</th>
                            <th>Office Hours</th>
                            <th class="text-end">Base Salary</th>
                            <th class="text-center">Days in Month</th>
                            <th class="text-center">Expected Days</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent</th>
                            <th class="text-end">Deduction</th>
                            <th class="text-end">Net Salary</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $row)
                        @php $counselor = $row['counselor']; @endphp
                        <tr>
                            <td>
                                {{ $counselor->name }}<br>
                                <small class="text-muted">{{ $counselor->email }}</small>
                            </td>
                            <td>{{ $counselor->joining_date?->format('d-m-Y') ?? '—' }}</td>
                            <td>
                                @if($counselor->office_start_time && $counselor->office_end_time)
                                    {{ \Carbon\Carbon::parse($counselor->office_start_time)->format('h:i A') }}
                                    –
                                    {{ \Carbon\Carbon::parse($counselor->office_end_time)->format('h:i A') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">₹{{ number_format($row['base_salary'], 2) }}</td>
                            <td class="text-center">{{ $row['days_in_month'] }}</td>
                            <td class="text-center">{{ $row['expected_working_days'] }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $row['attended_days'] }}</span>
                            </td>
                            <td class="text-center">
                                @if($row['absent_days'] > 0)
                                    <span class="badge bg-danger">{{ $row['absent_days'] }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-end text-danger">₹{{ number_format($row['deduction'], 2) }}</td>
                            <td class="text-end fw-semibold">₹{{ number_format($row['net_salary'], 2) }}</td>
                            <td>
                                <a href="{{ account_route('counselor-salaries.show', ['id' => $counselor->id, 'month' => $selectedMonth]) }}"
                                   class="btn btn-sm btn-outline-primary">Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">No counselors with salary and working days configured.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($salaries->isNotEmpty())
                    <tfoot>
                        <tr class="fw-semibold">
                            <td colspan="9" class="text-end">Total Net Salary</td>
                            <td class="text-end">₹{{ number_format($salaries->sum('net_salary'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
