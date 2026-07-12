<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadPayment;
use App\Models\Counselor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Helpers\LeadStatus; // Add this import
use App\Services\CounselorWorkingHoursService;
class DashboardController extends Controller
{
    protected $academicYear;

    public function __construct(
        private CounselorWorkingHoursService $workingHoursService
    ) {
        $this->academicYear = session('academic_year_id');
    }

    public function index()
    {
        // Get counts for different lead statuses
        $leadsCount = [
            'new' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $this->academicYear)->where('status', 'New')->count(),
            'warm' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $this->academicYear)->where('status', 'Warm')->count(),
            'hot' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $this->academicYear)->where('status', 'Hot')->count(),
            'application' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $this->academicYear)->where('status', 'Application')->count(),
            'admission' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $this->academicYear)->where('status', 'Admission')->count(),
            'total' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $this->academicYear)->count(),
        ];

        // Get previous academic year id (assuming academic_year_id is integer and sequential)
        $prevAcademicYear = $this->academicYear ? ($this->academicYear - 1) : null;

        $prevLeadsCount = [
            'new' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $prevAcademicYear)->where('status', 'New')->count(),
            'warm' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $prevAcademicYear)->where('status', 'Warm')->count(),
            'hot' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $prevAcademicYear)->where('status', 'Hot')->count(),
            'application' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $prevAcademicYear)->where('status', 'Application')->count(),
            'admission' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $prevAcademicYear)->where('status', 'Admission')->count(),
            'total' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $prevAcademicYear)->count(),
        ];

        // Calculate percentage difference for each status
        $leadsPercentageDiff = [];
        foreach ($leadsCount as $key => $current) {
            $prev = $prevLeadsCount[$key] ?? 0;
            if ($prev > 0) {
            $leadsPercentageDiff[$key] = round((($current - $prev) / $prev) * 100, 1);
            } else {
            $leadsPercentageDiff[$key] = $current > 0 ? 100 : 0;
            }
        }

        // Get funnel data
        $funnelData = [
            'total' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $this->academicYear)->count(),
            'positive' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $this->academicYear)->whereIn('status', ['Warm', 'Hot'])->count(),
            'application' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $this->academicYear)->where('status', 'Application')->count(),
            'admission' => Lead::where('counselor_id', auth()->guard('counselor')->user()->id)->where('academic_year_id', $this->academicYear)->where('status', 'Admission')->count()
        ];

        // Calculate percentages for funnel
        $funnelPercentages = [];
        if ($funnelData['total'] > 0) {
            foreach ($funnelData as $key => $value) {
                $funnelPercentages[$key] = round(($value / $funnelData['total']) * 100, 1);
            }
        }

        // Get top performing counselor
        $topCounselor = Counselor::withCount(['leads' => function($query) {
            $query->where('counselor_id', auth()->guard('counselor')->user()->id)
            ->where('status', 'Admission');
        }])
        ->orderBy('leads_count', 'desc')
        ->first();

        // Get monthly leads data
        $monthlyLeads = Lead::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('counselor_id', auth()->guard('counselor')->user()->id)
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->get()
        ->pluck('count', 'month')
        ->toArray();

        // Format monthly data for chart
        $monthlyLeadsData = array_fill(1, 12, 0);
        foreach ($monthlyLeads as $month => $count) {
            $monthlyLeadsData[$month] = $count;
        }

        // Get leads status distribution for pie chart
        $leadStatusDistribution = Lead::select('status', DB::raw('count(*) as count'))
            ->where('counselor_id', auth()->guard('counselor')->user()->id)
            ->where('academic_year_id', $this->academicYear)
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Get conversion rate
        $conversionRate = 0;
        if ($leadsCount['total'] > 0) {
            $conversionRate = round(($leadsCount['admission'] / $leadsCount['total']) * 100, 1);
        }

        // Get current year's months
        $currentYear = date('Y');
        $currentMonth = date('n');

        // Replace the followups statistics query
        $followupsData = collect();
        for ($month = 1; $month <= $currentMonth; $month++) {
            $monthData = DB::table('lead_contact_logs')
                ->join('leads', 'lead_contact_logs.lead_id', '=', 'leads.id')
                ->select([
                    DB::raw('COUNT(CASE WHEN response_type = "Positive" THEN 1 END) as positive_count'),
                    DB::raw('COUNT(CASE WHEN response_type != "Positive" THEN 1 END) as negative_count')
                ])
                ->whereYear('lead_contact_logs.created_at', $currentYear)
                ->whereMonth('lead_contact_logs.created_at', $month)
                ->where('leads.counselor_id', auth()->guard('counselor')->user()->id)
                ->whereRaw('lead_contact_logs.created_at > leads.received_at')
                ->first();

            $followupsData->push([
                'month' => date('M', mktime(0, 0, 0, $month, 1)),
                'positive_count' => $monthData->positive_count ?? 0,
                'negative_count' => $monthData->negative_count ?? 0
            ]);
        }

        // Add months array for x-axis labels
        $months = $followupsData->pluck('month')->toArray();

        // Get status colors for charts
        $statusColors = [];
        foreach ($leadStatusDistribution as $status => $count) {
            $statusColors[] = LeadStatus::getHexColor($status);
        }

        // For funnel chart specific colors
        $funnelColors = [
            LeadStatus::getColor('New'),      // For total
            LeadStatus::getColor('Warm'),     // For positive
            LeadStatus::getColor('Application'), // For application
            LeadStatus::getColor('Admission')  // For admission
        ];

        $workingHours = $this->workingHoursService->getTodaySummary(
            auth()->guard('counselor')->user()
        );

        return view('counselor.dashboard', compact(
            'leadsCount',
            'topCounselor',
            'monthlyLeadsData',
            'leadStatusDistribution',
            'conversionRate',
            'followupsData',
            'funnelData',
            'funnelPercentages',
            'leadsPercentageDiff',
            'statusColors',
            'funnelColors',
            'months',
            'workingHours'
        ));
    }
}
