<?php

namespace App\Http\Controllers\Counselor;

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

        // Get all required data for dropdowns
        $leads = $query->where('academic_year_id', $this->academicYear)
        ->where('counselor_id', auth()->guard('counselor')->user()->id)
        ->latest()->get();
        $colleges = College::all();
        $courses = Course::all();
        $sources = Source::all();
        $statuses = LeadStatus::getAllStatuses();

        return view('counselor.reports.leads', compact('leads', 'colleges', 'courses', 'sources', 'statuses'));
    }


    public function callLogs(Request $request)
    {
        $query = LeadContactLog::query()
            ->with(['lead', 'lead.counselor'])
            ->whereHas('lead', function($q) {
                $q->where('academic_year_id', $this->academicYear)
                ->where('counselor_id', auth()->guard('counselor')->user()->id);
            })
            ->orderBy('contact_date', 'desc');

        // Apply filters if provided
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('contact_date', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }

        $logs = $query->get();
        return view('counselor.reports.call-logs', compact('logs'));
    }

    public function counselorPerformance(Request $request)
    {
        // Get counselors list
        $counselors = Counselor::select('id', 'name')->orderBy('name')->get();
        
        // Initialize query
        $query = Lead::query();
        
        // Apply filters
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59'
            ]);
        }
        
        $query->where('academic_year_id', $this->academicYear)
        ->where('counselor_id', auth()->guard('counselor')->user()->id);

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

        return view('counselor.reports.counselor-performance', compact(
            'counselors',
            'metrics',
            'sourceWiseLeads',
            'funnelData'
        ));
    }

    
}
