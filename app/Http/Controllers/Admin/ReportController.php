<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;
use App\Models\College;
use App\Models\Course;
use App\Models\Source;
use App\Models\Counselor;
use App\Models\Lead;
use App\Helpers\LeadStatus;
use App\Models\LeadContactLog;
use App\Models\LeadPayment;
use App\Models\LeadTransfer;
use Illuminate\Support\Facades\DB;
use App\Models\AcademicYear;
class ReportController extends Controller
{
    public function __construct()
    {
        $this->academicYear = session('academic_year_id');
    }

    public function leads(Request $request)
    {
        $query = Lead::query();

        // Apply filters if provided
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        if ($request->college_id) {
            $query->whereIn('college_id', $request->college_id);
        }

        if ($request->course_id) {
            $query->whereIn('course_id', $request->course_id);
        }

        if ($request->source_id) {
            $query->whereIn('source_id', $request->source_id);
        }

        if ($request->status) {
            $query->whereIn('status', $request->status);
        }

        if ($request->counselor_id) {
            $query->whereIn('counselor_id', $request->counselor_id);
        }

        // Get all required data for dropdowns
        $leads = $query->where('academic_year_id', $this->academicYear)->latest()->get();
        $colleges = College::all();
        $courses = Course::all();
        $sources = Source::all();
        $counselors = Counselor::where('status', 1)->get();
        $statuses = LeadStatus::getAllStatuses();

        return view('admin.reports.leads', compact('leads', 'colleges', 'courses', 'sources', 'counselors', 'statuses'));
    }

    public function pendingFollowups()
    {
        $counselors = Counselor::withCount(['leads' => function($query) {
            $query->whereNotNull('next_follow_up')
                ->where('next_follow_up', '<', now())
                ->where('academic_year_id', $this->academicYear);
        }])->having('leads_count', '>', 0)
        ->get();
        
        return view('admin.reports.pending-followups', compact('counselors'));
    }

    public function showPendingFollowups($counselor_id)
    {
        $counselor = Counselor::findOrFail($counselor_id);
        
        $leads = Lead::where('counselor_id', $counselor_id)
            ->where('academic_year_id', $this->academicYear)
            ->whereNotNull('next_follow_up')
            ->where('next_follow_up', '<', now())
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'enrolled')
            ->with(['college', 'course', 'source'])
            ->latest()
            ->get();

        return view('admin.reports.pending-followups-list', compact('leads', 'counselor'));
    }

    public function callLogs(Request $request)
    {
        $query = LeadContactLog::query()
            ->with(['lead', 'lead.counselor'])
            ->whereHas('lead', function($q) {
                $q->where('academic_year_id', $this->academicYear);
            })
            ->orderBy('contact_date', 'desc');

        // Apply filters if provided
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('contact_date', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        if ($request->counselor_id) {
            $query->whereHas('lead', function($q) use ($request) {
                $q->where('counselor_id', $request->counselor_id);
            });
        }

        $logs = $query->get();
        $counselors = Counselor::select('id', 'name')->where('status', 1)->orderBy('name')->get();

        return view('admin.reports.call-logs', compact('logs', 'counselors'));
    }

    public function payments(Request $request)
    {
        $query = LeadPayment::query()
            ->with(['lead', 'lead.college'])
            ->orderBy('payment_date', 'desc');

        // Apply filters
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('payment_date', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        if ($request->type) {
            $query->where('transaction_type', $request->type);
        }

        $payments = $query->get();
        $paymentTypes = [
            1 => 'Received From Student',
            2 => 'Received From Agent',
            3 => 'Received From College',
            4 => 'Paid To Student',
            5 => 'Paid To Agent',
            6 => 'Paid To College',
            7 => 'Other'
        ];

        $transactionTypes = transaction_types();

        return view('admin.reports.payments', compact('payments', 'paymentTypes', 'transactionTypes'));
    }

    public function analytics()
    {
        // Get leads by source
        $leadsBySource = Lead::select('source_id', DB::raw('count(*) as count'))
            ->where('academic_year_id', $this->academicYear)
            ->with('source')
            ->groupBy('source_id')
            ->get()
            ->mapWithKeys(function($lead) {
                return [$lead->source->name ?? 'Unknown' => $lead->count];
            })
            ->toArray(); // Convert collection to array

        // Get conversions by month (current year)
        $monthlyConversions = Lead::select(
            DB::raw('MONTH(admission_date) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('academic_year_id', $this->academicYear)
        ->whereIn('status', ['Admission'])
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->get()
        ->map(function($item) {
            return [
                'month' => date('M', mktime(0, 0, 0, $item->month, 1)),
                'count' => $item->count
            ];
        });

        // Get admissions by college
        $admissionsByCollege = Lead::where('status', 'Admission')
            ->select('college_id', DB::raw('count(*) as count'))
            ->where('academic_year_id', $this->academicYear)
            ->with('college')
            ->groupBy('college_id')
            ->get()
            ->mapWithKeys(function($lead) {
                return [$lead->college->name ?? 'Unknown' => $lead->count];
            })
            ->toArray(); // Convert collection to array

        // Get leads by month
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        $leadsByMonth = collect();
        for ($month = 1; $month <= 12; $month++) {
            $count = Lead::whereYear('created_at', $currentYear)
                ->where('academic_year_id', $this->academicYear)
                ->whereMonth('created_at', $month)
                ->count();
                
            $leadsByMonth->put($month, $count);
        }

        // Convert to array with all months (0 for months without data)
        $leadsByMonthData = $leadsByMonth->toArray();


        // Get course-wise sales report
        $salesReport = Course::select(
            'courses.name as course',
            DB::raw('COUNT(CASE WHEN leads.status = "Application" THEN 1 END) as applications'),
            DB::raw('COUNT(CASE WHEN leads.status = "Reservation" THEN 1 END) as reservations'),
            DB::raw('COUNT(CASE WHEN leads.status = "Admission" THEN 1 END) as admissions'),
            DB::raw('COUNT(CASE WHEN leads.status = "Cancelled" THEN 1 END) as cancellations')
        )
        ->leftJoin('leads', 'courses.id', '=', 'leads.course_id')
        ->where('leads.academic_year_id', $this->academicYear)
        ->groupBy('courses.id', 'courses.name')
        ->get();

        return view('admin.reports.analytics', compact(
            'leadsBySource',
            'monthlyConversions',
            'admissionsByCollege',
            'leadsByMonth',
            'salesReport',
            'leadsByMonthData'
        ));
    }

    public function counselorPerformance(Request $request)
    {
        // Get counselors list
        $counselors = Counselor::select('id', 'name')->where('status', 1)->orderBy('name')->get();
        
        // Initialize query
        $query = Lead::query();
        
        // Apply filters
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }
        
        if ($request->counselor_id) {
            $query->where('counselor_id', $request->counselor_id);
        }
        $query->where('academic_year_id', $this->academicYear);

        // Get performance metrics
        $metrics = [
            'total_leads' => $query->count(),
            'applications' => (clone $query)->where('status', 'Application')->count(),
            'admissions' => (clone $query)->where('status', 'Admission')->count(),
            'reservations' => (clone $query)->where('status', 'Reservation')->count(),
            'cancellations' => (clone $query)->where('status', 'Cancelled')->count(),
        ];

        // Calculate conversion rate
        $metrics['conversion_rate'] = $metrics['total_leads'] > 0 
            ? round(($metrics['admissions'] / $metrics['total_leads']) * 100, 1)
            : 0;

        // Get source wise leads data
        $sourceWiseLeads = (clone $query)
            ->select('source_id', DB::raw('count(*) as count'))
            ->with('source')
            ->groupBy('source_id')
            ->get()
            ->mapWithKeys(function($lead) {
                return [$lead->source->name ?? 'Unknown' => $lead->count];
            })
            ->toArray(); 

        // Get lead funnel data
        $funnelData = [
            'total' => $metrics['total_leads'],
            'applications' => $metrics['applications'],
            'admissions' => $metrics['admissions'],
            'reservations' => $metrics['reservations'],
            'cancellations' => $metrics['cancellations'],
        ];

        // Get leads list
        $leads = $query->with(['counselor', 'source'])
            ->select('id', 'lead_id', 'name', 'personal_email', 'mobile', 'counselor_id', 'status', 'created_at')
            ->latest()
            ->get();
            
        if ($request->counselor_id) {
            $unseen = Lead::where('counselor_id', $request->counselor_id)->where('transfer_seen', false)->count();
            $pendingFL = Lead::where('counselor_id', $request->counselor_id)->where('next_follow_up', '<', today())
                ->whereNotIN('status', ['Converted', 'Bin'])
                ->count();
            $todaysFL = Lead::where('counselor_id', $request->counselor_id)->whereDate('next_follow_up', today())
                ->where('status', '!=', 'Converted')
                ->count();
            $tomorrowsFL = Lead::where('counselor_id', $request->counselor_id)->whereDate('next_follow_up', today()->addDay())
                ->where('status', '!=', 'Converted')
                ->count();
            $bin = Lead::where('counselor_id', $request->counselor_id)->where('status', 'Bin')->count();
            $selecetdCounselor = $request->counselor_id;
        } else {
            $unseen = 0;
            $pendingFL = 0;
            $todaysFL = 0;
            $tomorrowsFL = 0;
            $bin = 0;
            $selecetdCounselor = '';
        }

        return view('admin.reports.counselor-performance', compact(
            'counselors',
            'metrics',
            'sourceWiseLeads',
            'funnelData',
            'leads',
            'unseen',
            'pendingFL',
            'todaysFL',
            'tomorrowsFL',
            'bin',
            'selecetdCounselor'
        ));
    }

    public function transfer(Request $request)
    {
        // Get counselors for filter
        $counselors = Counselor::select('id', 'name')->where('status', 1)->orderBy('name')->get();

        // Initialize query
        $query = LeadTransfer::with(['lead', 'fromCounselor', 'toCounselor'])
            ->whereHas('lead', function($q) {
                $q->where('academic_year_id', $this->academicYear);
            })
            ->orderBy('created_at', 'desc');

        // Apply date filters
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        // Apply counselor filter
        if ($request->counselor_id) {
            $query->where(function($q) use ($request) {
                $q->where('from_counselor_id', $request->counselor_id)
                ->orWhere('to_counselor_id', $request->counselor_id);
            });
        }

        $transfers = $query->get();

        return view('admin.reports.transfer', compact('transfers', 'counselors'));
    }

    public function agentCommission(Request $request)
    {
        // Initialize query
        $query = Lead::whereNotNull('agent_id')
            ->where('academic_year_id', $this->academicYear)
            ->with(['agent', 'payments', 'college']);

        // Apply date filters if provided
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        // Get agent wise commission data
        $agentCommissions = $query->get()
            ->groupBy('agent_id')
            ->map(function($leads) {
                $firstLead = $leads->first();
                return [
                    'agent_name' => $firstLead->agent->name ?? 'N/A',
                    'total_leads' => $leads->count(),
                    'total_admissions' => $leads->where('status', 'Admission')->count(),
                    'total_commission' => $leads->sum('agent_commission'),
                    'leads' => $leads->map(function($lead) {
                        return [
                            'id' => $lead->id,
                            'lead_id' => $lead->lead_id,
                            'name' => $lead->name,
                            'college' => $lead->college->name ?? 'N/A',
                            'status' => $lead->status,
                            'agent_commission' => $lead->agent_commission,
                            'commission_paid' => $lead->payments
                                ->where('transaction_type', 5)
                                ->sum('amount'),
                            'admission_date' => $lead->admission_date
                        ];
                    })
                ];
            });

        // Get summary
        $summary = [
            'total_leads' => $query->count(),
            'total_admissions' => $query->where('status', 'Admission')->count(),
            'total_commission_paid' => LeadPayment::whereIn('lead_id', $query->pluck('id'))
                ->where('transaction_type', 5)
                ->sum('amount')
        ];

        return view('admin.reports.agent-commission', compact('agentCommissions', 'summary'));
    }
    
    public function consolidated(Request $request)
    {
        // Resolve academic year from session, falling back to active / latest
        $academicYear = AcademicYear::find($this->academicYear)
            ?? AcademicYear::where('is_active', true)->first()
            ?? AcademicYear::orderByDesc('id')->first();

        if ($academicYear && !$this->academicYear) {
            session([
                'academic_year_id' => $academicYear->id,
                'academic_year_name' => $academicYear->name,
            ]);
            $this->academicYear = $academicYear->id;
        }

        $counselors = Counselor::select('id', 'name')->where('status', 1)->orderBy('name')->get();

        $consolidatedData = [];
        $today = \Carbon\Carbon::today();
        $tomorrow = \Carbon\Carbon::tomorrow();
        $academicYearId = $academicYear?->id;

        foreach ($counselors as $counselor) {
            $baseQuery = Lead::where('counselor_id', $counselor->id);
            if ($academicYearId) {
                $baseQuery->where('academic_year_id', $academicYearId);
            }
            
            // Total leads
            $totalLeads = (clone $baseQuery)->count();
            
            // Pending followups (leads with next_follow_up in the past)
            $pendingFollowups = (clone $baseQuery)
                ->where('next_follow_up', '<', now())
                ->whereNotNull('next_follow_up')
                ->count();
            
            // Today's followups
            $todayFollowups = (clone $baseQuery)
                ->whereDate('next_follow_up', $today)
                ->count();
            
            // Tomorrow's followups
            $tomorrowFollowups = (clone $baseQuery)
                ->whereDate('next_follow_up', $tomorrow)
                ->count();
            
            // Applications
            $applications = (clone $baseQuery)
                ->where('status', 'Application')
                ->count();
            
            // Admissions
            $admissions = (clone $baseQuery)
                ->where('status', 'Admission')
                ->count();
            
            // Calculate conversion rate
            $conversionRate = $totalLeads > 0 ? round(($admissions / $totalLeads) * 100, 2) : 0;
            
            $consolidatedData[] = [
                'counselor_id' => $counselor->id,
                'counselor_name' => $counselor->name,
                'total_leads' => $totalLeads,
                'pending_followups' => $pendingFollowups,
                'today_followups' => $todayFollowups,
                'tomorrow_followups' => $tomorrowFollowups,
                'applications' => $applications,
                'admissions' => $admissions,
                'conversion_rate' => $conversionRate
            ];
        }
        
        // Calculate totals
        $totals = [
            'total_leads' => array_sum(array_column($consolidatedData, 'total_leads')),
            'pending_followups' => array_sum(array_column($consolidatedData, 'pending_followups')),
            'today_followups' => array_sum(array_column($consolidatedData, 'today_followups')),
            'tomorrow_followups' => array_sum(array_column($consolidatedData, 'tomorrow_followups')),
            'applications' => array_sum(array_column($consolidatedData, 'applications')),
            'admissions' => array_sum(array_column($consolidatedData, 'admissions')),
        ];
        
        // Calculate overall conversion rate
        $totals['conversion_rate'] = $totals['total_leads'] > 0 
            ? round(($totals['admissions'] / $totals['total_leads']) * 100, 2) 
            : 0;
        
        return view('admin.reports.consolidated', compact('consolidatedData', 'totals', 'academicYear'));
    }
    
    public function pickedLeads(Request $request)
    {
        // Get all counselors
        $counselors = Counselor::select('id', 'name')->where('status', 1)->orderBy('name')->get();
        
        $pickedLeadsData = [];
        $totalPickedLeads = 0;
        
        foreach ($counselors as $counselor) {
            // Base query for picked leads in current academic year
            $baseQuery = Lead::where('counselor_id', $counselor->id)
                            ->whereNotNull('picked_at');
            
            // Apply date filters if provided
            if ($request->from_date && $request->to_date) {
                $baseQuery->whereBetween('picked_at', [
                    $request->from_date . ' 00:00:00',
                    $request->to_date . ' 23:59:59'
                ]);
            }
            
            // Get picked leads count
            $pickedLeadsCount = $baseQuery->count();
            
            // Get picked leads details for the table
            $pickedLeads = $baseQuery->with(['counselor', 'source', 'college', 'course'])
                                    ->latest('picked_at')
                                    ->get();
            
            $pickedLeadsData[] = [
                'counselor_id' => $counselor->id,
                'counselor_name' => $counselor->name,
                'picked_leads_count' => $pickedLeadsCount,
                'picked_leads' => $pickedLeads
            ];
            
            $totalPickedLeads += $pickedLeadsCount;
        }
        
        // Get summary data
        $summary = [
            'total_counselors' => $counselors->count(),
            'total_picked_leads' => $totalPickedLeads,
            'average_picked_per_counselor' => $counselors->count() > 0 ? round($totalPickedLeads / $counselors->count(), 2) : 0
        ];
        
        return view('admin.reports.picked-leads', compact('pickedLeadsData', 'summary'));
    }
}
