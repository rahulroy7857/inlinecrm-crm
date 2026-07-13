<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use App\Models\Source;
use App\Models\Counselor;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\College;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeadsImport;
use Illuminate\Support\Facades\Log; 
use App\Models\Timeline;
use App\Models\Holiday;
use App\Helpers\LeadStatus;
use App\Models\LeadTransfer;
use App\Models\Agent;
use DB;
class LeadController extends Controller
{
    public function __construct()
    {
        $this->academicYear = session('academic_year_id');
    }

    protected function scopeAcademicYear($query)
    {
        if ($this->academicYear) {
            $query->where('academic_year_id', $this->academicYear);
        }

        return $query;
    }

    public function show($id)
    {
        $lead = Lead::with([
            'source',
            'academicYear',
            'counselor',
            'course',
            'college',
            'education',
            'exams',
            'payments.accountTransaction.ledgerAccount',
            'timeline',
            'student.payments',
            'contactLogs' => function($query) {
                $query->orderBy('contact_date', 'desc');
            }
        ])
        ->where('counselor_id', auth()->guard('counselor')->user()->id)
        ->findOrFail($id);

        // Get data for dropdowns
        $sources = Source::select('id', 'name')->get()->map(function($item) {
            return ['value' => $item->id, 'text' => $item->name];
        });
        
        $counselors = Counselor::select('id', 'name')->where('status', 1)->get()->map(function($item) {
            return ['value' => $item->id, 'text' => $item->name];
        });
        
        $academicYears = AcademicYear::select('id', 'name')->get()->map(function($item) {
            return ['value' => $item->id, 'text' => $item->name];
        });
        
        $courses = Course::select('id', 'name')->get()->map(function($item) {
            return ['value' => $item->id, 'text' => $item->name];
        });
        
        $colleges = College::select('id', 'name')->get()->map(function($item) {
            return ['value' => $item->id, 'text' => $item->name];
        });
        $statuses = LeadStatus::getAllStatuses();

        // Get countries list
        $countries = countries();
        $states = ["Any", "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh", "Goa", "Gujarat", 
        "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala", "Madhya Pradesh", 
        "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab", 
        "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh", 
        "Uttarakhand", "West Bengal"];

        // Get upcoming holidays
        $holidays = Holiday::where('holiday_date', '>=', now()->startOfDay())
                      ->orderBy('holiday_date')
                      ->limit(3)
                      ->get()
                      ->map(function($holiday) {
                          return [
                              'date' => $holiday->holiday_date->format('Y-m-d'),
                              'name' => $holiday->title
                          ];
                      });
        $agents = Agent::where('status', 'active')
            ->get()
            ->map(function($item) {
                return ['value' => $item->id, 'text' => $item->name];
            });

        if ($lead->transfer_seen == false && request()->boolean('acknowledge')) {
            $lead->transfer_seen = true;
            $lead->save();
        }
        $ledgerAccounts = \App\Models\LedgerAccount::where('status', 'Active')->orderBy('name')->get();
        return view('counselor.lead-profile', compact(
            'lead',
            'sources',
            'counselors',
            'academicYears',
            'courses',
            'colleges',
            'countries',
            'states',
            'holidays',
            'statuses',
            'agents',
            'ledgerAccounts'
        ));
    }

    public function newLeads()
    {
        $leads = Lead::with([
            'source',
            'academicYear',
            'counselor',
            'course',
            'college',
            'education',
            'exams',
            'payments'
        ])
        ->where('counselor_id', auth()->guard('counselor')->user()->id)
        ->where('transfer_seen', false)
        ->orderBy('created_at', 'desc')->get();

        $sources = Source::select('id', 'name')->get()->map(function($item) {
            return ['value' => $item->id, 'text' => $item->name];
        });

        // Get courses for dropdown
        $courses = Course::select('id', 'name')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get academic years for dropdown
        $academicYears = AcademicYear::select('id', 'name')
            ->orderByDesc('name')
            ->get();
    
        // Get countries list
        $countries = countries();

        return view('counselor.new-leads', compact('leads', 'sources', 'countries', 'courses', 'academicYears'));
    }
    
    public function leadsBasket()
    {
        $leads = Lead::with([
            'source',
            'academicYear',
            'counselor',
            'course',
            'college',
            'education',
            'exams',
            'payments'
        ])
        ->whereNULL('counselor_id')
        ->orderBy('created_at', 'desc')->get();

        $sources = Source::select('id', 'name')->get()->map(function($item) {
            return ['value' => $item->id, 'text' => $item->name];
        });

        // Get courses for dropdown
        $courses = Course::select('id', 'name')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get academic years for dropdown
        $academicYears = AcademicYear::select('id', 'name')
            ->orderByDesc('name')
            ->get();
    
        // Get countries list
        $countries = countries();

        return view('counselor.leads-basket', compact('leads', 'sources', 'countries', 'courses', 'academicYears'));
    }
    
    public function pickLead($id)
    {
        $counselor = auth()->guard('counselor')->user();

        try {
            DB::beginTransaction();

            $lead = Lead::whereNull('counselor_id')
                ->lockForUpdate()
                ->findOrFail($id);

            $lead->update([
                'counselor_id' => $counselor->id,
                'transfer_seen' => false,
                'received_at' => now(),
                'picked_at' => now(),
            ]);

            Timeline::create([
                'lead_id' => $lead->id,
                'title' => 'Picked Lead',
                'description' => "Lead picked by {$counselor->name}",
                'event_type' => 'manual',
                ...Timeline::performerAttributes($counselor),
                'event_date' => now(),
            ]);

            ActivityLogger::log(
                "Picked lead {$lead->name} by counselor {$counselor->name}",
                'Pick',
                $counselor,
                ['lead' => $lead->id, 'counselor' => $counselor->id]
            );

            DB::commit();

            return redirect()
                ->route('counselor.new-leads')
                ->with('success', "Lead assigned to {$counselor->name} successfully. Check New Leads.");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Lead already picked by another counselor.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to pick lead. Please try again.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'mobile' => 'required|string',
                'email' => 'required|email',
                'course_id' => 'required|exists:courses,id',
                'academic_year_id' => 'required|exists:academic_years,id',
                'source_id' => 'required|exists:sources,id',
                'country' => 'required|string',
                'state' => 'required|string',
                'existing_lead_id' => 'nullable|exists:leads,id',
            ]);

            $leadData = [
                'name' => $request->name,
                'mobile' => $request->mobile,
                'personal_email' => $request->email,
                'course_id' => $request->course_id,
                'academic_year_id' => $request->academic_year_id,
                'source_id' => $request->source_id,
                'country' => $request->country,
                'state' => $request->state,
            ];

            $existingLead = $request->existing_lead_id
                ? Lead::find($request->existing_lead_id)
                : Lead::findDuplicateByContact($request->mobile, $request->email);

            if ($existingLead) {
                $existingLead->update($leadData);

                Timeline::create([
                    'lead_id' => $existingLead->id,
                    'title' => 'Lead Updated',
                    'description' => "Lead updated with name: {$existingLead->name} (duplicate match)",
                    'event_type' => 'manual',
                    ...Timeline::performerAttributes(),
                    'event_date' => now(),
                ]);

                ActivityLogger::log(
                    "Updated existing lead: {$existingLead->name} ({$existingLead->lead_id})",
                    'Update',
                    auth()->guard('counselor')->user(),
                    ['lead' => $existingLead->id]
                );

                return redirect()->back()->with('success', "Lead updated successfully ({$existingLead->lead_id}).");
            }

            $lead = Lead::create([
                ...$leadData,
                'status' => 'New',
                'next_follow_up' => now()->addDays(1),
            ]);

            Timeline::create([
                'lead_id' => $lead->id,
                'title' => 'New Lead Created',
                'description' => "Lead created with name: {$lead->name}",
                'event_type' => 'manual',
                ...Timeline::performerAttributes(),
                'event_date' => now(),
            ]);

            ActivityLogger::log(
                "Created new lead: {$lead->name}",
                'Create',
                auth()->guard('counselor')->user(),
                ['lead' => $lead->id]
            );

            return redirect()->back()->with('success', 'Lead created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating lead: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $field = $request->name;
        $value = $request->value;

        $dateFields = ['dob', 'application_date', 'reservation_date', 'admission_date', 'cancel_date'];
        if (in_array($field, $dateFields, true) && $value) {
            $value = parse_editable_date($value);
        }

        $oldValue = $lead->$field;
        $lead->$field = $value;
        $lead->save();

        ActivityLogger::log(
            "Updated lead {$lead->name}'s {$field}",
            'Update',
            auth()->guard('counselor')->user(),
            [
                'lead' => $lead->id,
                'field' => $field,
                'old_value' => $oldValue,
                'new_value' => $value
            ]
        );

        return response()->json(['success' => true]);
    }

    // public function destroy($id)
    // {
    //     $lead = Lead::findOrFail($id);
    //     $lead->delete();

    //     ActivityLogger::log(
    //         "Deleted lead {$lead->name}",
    //         'Delete',
    //         auth()->guard('counselor')->user(),
    //         ['lead' => $lead->id]
    //     );

    //     Timeline::create([
    //         'lead_id' => $lead->id,
    //         'title' => 'Deleted Lead',
    //         'description' => "Lead deleted with name: {$lead->name}",
    //         'event_type' => 'manual',
    //         'performed_by' => auth()->id(),
    //         'event_date' => now(),
    //     ]);

    //     if (request()->ajax()) {
    //         return response()->json(['success' => true]);
    //     }

    //     return redirect('/counselor/new-leads')->with('success', 'Lead deleted successfully.');
    // }

    public function pendingFollowups()
    {
        $leads = $this->scopeAcademicYear(Lead::query())
            ->with(['source', 'course'])
            ->where('counselor_id', auth()->guard('counselor')->user()->id)
            ->where('transfer_seen', true)
            ->where('next_follow_up', '<', today())
            ->whereNotIn('status', ['Converted', 'Bin'])
            ->orderBy('next_follow_up')
            ->get();

        return view('counselor.followups.pending', compact('leads'));
    }

    public function todayFollowups()
    {
        $leads = $this->scopeAcademicYear(Lead::query())
            ->with(['source', 'course'])
            ->where('counselor_id', auth()->guard('counselor')->user()->id)
            ->where('transfer_seen', true)
            ->whereDate('next_follow_up', today())
            ->where('status', '!=', 'Converted')
            ->orderBy('next_follow_up')
            ->get();

        return view('counselor.followups.today', compact('leads'));
    }

    public function tomorrowFollowups()
    {
        $leads = $this->scopeAcademicYear(Lead::query())
            ->with(['source', 'course'])
            ->where('counselor_id', auth()->guard('counselor')->user()->id)
            ->where('transfer_seen', true)
            ->whereDate('next_follow_up', today()->addDay())
            ->where('status', '!=', 'Converted')
            ->orderBy('next_follow_up')
            ->get();

        return view('counselor.followups.tomorrow', compact('leads'));
    }

    public function verifyLead(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'email' => 'required|email'
        ]);

        $duplicates = [];
        $existingLead = Lead::findDuplicateByContact($request->mobile, $request->email);

        if ($existingLead) {
            if (in_array($request->mobile, [
                $existingLead->mobile,
                $existingLead->alternative_mobile,
                $existingLead->father_mobile,
                $existingLead->mother_mobile,
                $existingLead->guardian_mobile
            ], true)) {
                $duplicates['mobile'] = [
                    'lead_id' => $existingLead->lead_id,
                    'name' => $existingLead->name,
                    'field' => $this->getMatchedPhoneField($existingLead, $request->mobile)
                ];
            }

            if (in_array($request->email, [
                $existingLead->personal_email,
                $existingLead->father_email,
                $existingLead->mother_email,
                $existingLead->guardian_email
            ], true)) {
                $duplicates['email'] = [
                    'lead_id' => $existingLead->lead_id,
                    'name' => $existingLead->name,
                    'field' => $this->getMatchedEmailField($existingLead, $request->email)
                ];
            }

            ActivityLogger::log(
                "Duplicate lead verification — will update existing record",
                'Verify',
                auth()->guard('counselor')->user(),
                [
                    'mobile' => $request->mobile,
                    'email' => $request->email,
                    'duplicates' => $duplicates,
                    'existing_lead_id' => $existingLead->id,
                ]
            );
        } else {
            ActivityLogger::log(
                "Lead verification passed (no duplicates)",
                'Verify',
                auth()->guard('counselor')->user(),
                [
                    'mobile' => $request->mobile,
                    'email' => $request->email
                ]
            );
        }

        return response()->json([
            'success' => true,
            'duplicates' => $duplicates,
            'can_proceed' => true,
            'is_update' => !empty($duplicates),
            'existing_lead_id' => $existingLead?->id,
        ]);
    }

    private function getMatchedPhoneField($lead, $phone)
    {
        $fields = [
            'mobile' => 'Primary Mobile',
            'alternative_mobile' => 'Alternative Mobile',
            'father_mobile' => 'Father Mobile',
            'mother_mobile' => 'Mother Mobile',
            'guardian_mobile' => 'Guardian Mobile'
        ];

        foreach ($fields as $field => $label) {
            if ($lead->$field === $phone) {
                return $label;
            }
        }

        return 'Mobile';
    }

    private function getMatchedEmailField($lead, $email)
    {
        $fields = [
            'personal_email' => 'Personal Email',
            'father_email' => 'Father Email',
            'mother_email' => 'Mother Email',
            'guardian_email' => 'Guardian Email'
        ];

        foreach ($fields as $field => $label) {
            if ($lead->$field === $email) {
                return $label;
            }
        }

        return 'Email';
    }

    public function search(Request $request)
    {
        if (!$request->has('column_name') || !$request->has('value')) {
            $leads = array();
            return view('counselor.search', compact('leads'));
        }
        $leads = Lead::query()
            ->where('counselor_id', auth()->guard('counselor')->user()->id)
            ->with(['source', 'course'])
            ->where(function($query) use ($request) {
                $column = $request->column_name;
                $value = $request->value;

                // If searching phone numbers
                if (in_array($column, ['mobile', 'alternative_mobile', 'father_mobile', 'mother_mobile', 'guardian_mobile'])) {
                    $query->where('mobile', 'LIKE', "%{$value}%")
                        ->orWhere('alternative_mobile', 'LIKE', "%{$value}%")
                        ->orWhere('father_mobile', 'LIKE', "%{$value}%")
                        ->orWhere('mother_mobile', 'LIKE', "%{$value}%")
                        ->orWhere('guardian_mobile', 'LIKE', "%{$value}%");
                }
                // If searching emails
                else if (in_array($column, ['personal_email', 'father_email', 'mother_email', 'guardian_email'])) {
                    $query->where('personal_email', 'LIKE', "%{$value}%")
                        ->orWhere('father_email', 'LIKE', "%{$value}%")
                        ->orWhere('mother_email', 'LIKE', "%{$value}%")
                        ->orWhere('guardian_email', 'LIKE', "%{$value}%");
                }
                // For other fields
                else {
                    $query->where($column, 'LIKE', "%{$value}%");
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('counselor.search', compact('leads'));
    }

    public function getCounts()
    {
        $counselorId = auth()->guard('counselor')->user()->id;
        $yearKey = $this->academicYear ?: 'all';
        $cacheKey = "counselor.{$counselorId}.lead_counts.{$yearKey}";

        $counts = cache()->remember($cacheKey, 15, function () use ($counselorId) {
            return [
                'basket_leads' => Lead::whereNull('counselor_id')->count(),
                'new_leads' => Lead::where('transfer_seen', false)->where('counselor_id', $counselorId)->count(),
                'today_followups' => $this->scopeAcademicYear(Lead::query())
                    ->where('counselor_id', $counselorId)
                    ->where('transfer_seen', true)
                    ->whereDate('next_follow_up', today())
                    ->where('status', '!=', 'Converted')
                    ->count(),
                'tomorrow_followups' => $this->scopeAcademicYear(Lead::query())
                    ->where('counselor_id', $counselorId)
                    ->where('transfer_seen', true)
                    ->whereDate('next_follow_up', today()->addDay())
                    ->where('status', '!=', 'Converted')
                    ->count(),
                'pending_followups' => $this->scopeAcademicYear(Lead::query())
                    ->where('counselor_id', $counselorId)
                    ->where('transfer_seen', true)
                    ->where('next_follow_up', '<', today())
                    ->whereNotIn('status', ['Converted', 'Bin'])
                    ->count(),
                'bin' => Lead::where('counselor_id', $counselorId)->where('status', 'Bin')->count(),
            ];
        });

        return response()->json($counts);
    }

    public function uploadLeads()
    {
        $sources = Source::select('id', 'name')->where('status', 'active')->get();
        return view('counselor.upload-leads', compact('sources'));
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'source_id' => 'required|exists:sources,id',
                'leads_file' => 'required|file|mimes:xlsx,xls|max:5120'
            ]);
            
            // Validate mandatory columns in the uploaded Excel file
            $file = $request->file('leads_file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $header = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];

            $requiredFields = ['name', 'mobile'];
            $missingFields = array_diff($requiredFields, array_map('strtolower', $header));

            if (!empty($missingFields)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing mandatory columns: ' . implode(', ', $missingFields)
                ], 422);
            }
            $import = new LeadsImport($request->source_id);
            Excel::import($import, $request->file('leads_file'));

            $successCount = $import->getSuccessCount();
            $errors = $import->getErrors();

            return response()->json([
                'success' => true,
                'message' => "{$successCount} leads imported successfully" . 
                            (count($errors) > 0 ? " with " . count($errors) . " errors" : ""),
                'count' => $successCount,
                'errors' => $errors,
            ]);

        } catch (\Exception $e) {
            Log::error('Upload failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 422);
        }
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'counselor_id' => 'required|exists:counselors,id',
            'next_fl_date' => 'required|date|after_or_equal:today'
        ]);

        $lead = Lead::where('id', $request->lead_id)->first();
        $fromCounselor = Counselor::where('id', $lead->counselor_id)->first();
        $fromCounselorName = $fromCounselor ? $fromCounselor->name : 'Unassigned';

        $lead->counselor_id = $request->counselor_id;
        $lead->next_follow_up = $request->next_fl_date;
        $lead->transfer_seen = false;
        $lead->received_at = now();
        $lead->save();

        // Create transfer record
        LeadTransfer::create([
            'lead_id' => $lead->id,
            'from_counselor_id' => $fromCounselor ? $fromCounselor->id : null,
            'to_counselor_id' => $request->counselor_id,
            'note' => $request->transfer_note,
            'transferred_by' => auth()->guard('counselor')->user()->name
        ]);

        Timeline::create([
                'lead_id' => $lead->id,
                'title' => 'Transferred Lead',
                'description' => "Lead transferred to counselor: {$lead->counselor->name}, from counselor: {$fromCounselorName}",
                'event_type' => 'manual',
                ...Timeline::performerAttributes(),
                'event_date' => now(),
        ]);

        ActivityLogger::log(
                "Transferred lead {$lead->name} to counselor {$lead->counselor->name}",
                'Transfer',
                auth()->guard('counselor')->user(),
                ['lead' => $lead->id, 'counselor' => $lead->counselor_id]
        );

        return redirect('/counselor/new-leads')->with('success', 'Leads transferred successfully');
    }

    public function updatePhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $lead = Lead::findOrFail($id);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($lead->photo && file_exists(public_path('storage/leads/' . $lead->photo))) {
                unlink(public_path('storage/leads/' . $lead->photo));
            }

            // Store new photo
            $photo = $request->file('photo');
            $filename = time() . '_' . $lead->id . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('storage/leads'), $filename);
            
            $lead->update(['photo' => $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Photo updated successfully',
                'photo' => url('storage/leads/' . $filename)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No photo uploaded'
        ], 400);
    }

    public function storeAdmission(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'admission_date' => 'required|date',
            'admission_no' => 'required|string|unique:leads,admission_no',
            'college_id' => 'required|exists:colleges,id',
            'course_id' => 'required|exists:courses,id',
            'commission' => 'nullable|numeric',
            'agent_commission' => 'nullable|numeric',
            'terms_and_conditions' => 'nullable|string',
            'agent_id' => 'nullable|exists:agents,id'
        ]);

        try {
            DB::beginTransaction();

            $lead = Lead::findOrFail($request->lead_id);
            
            // Update lead status and admission details
            $lead->update([
                'status' => 'Admission',
                'admission_date' => $validated['admission_date'],
                'admission_no' => $validated['admission_no'],
                'college_id' => $validated['college_id'],
                'course_id' => $validated['course_id'],
                'agent_id' => $validated['agent_id'] ?? null,
                'commission' => $validated['commission'],
                'agent_commission' => $validated['agent_commission'],
                'terms_and_conditions' => $validated['terms_and_conditions']
            ]);

            // Record the activity
            Timeline::create([
                'lead_id' => $lead->id,
                'title' => 'Admission Processed',
                'description' => "Admission processed for lead: {$lead->name}, Admission No: {$lead->admission_no}, College: {$lead->college->name}, Course: {$lead->course->name}",
                'event_type' => 'manual',
                ...Timeline::performerAttributes(),
                'event_date' => now(),
            ]);

            ActivityLogger::log(
                "Processed admission for lead {$lead->name} (Admission No: {$lead->admission_no})",
                'Admission',
                auth()->guard('counselor')->user(),
                [
                    'lead' => $lead->id,
                    'admission_no' => $lead->admission_no,
                    'college' => $lead->college_id,
                    'course' => $lead->course_id
                ]
            );

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Admission processed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to process admission: ' . $e->getMessage());
        }
    }

    public function storeApplication(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'application_date' => 'required|date',
            'application_note' => 'nullable|string',
            'college_id' => 'required|exists:colleges,id',
            'course_id' => 'required|exists:courses,id'
        ]);

        try {
            DB::beginTransaction();

            $lead = Lead::findOrFail($request->lead_id);
            
            // Update lead status and application details
            $lead->update([
                'status' => 'Application',
                'application_date' => $validated['application_date'],
                'application_note' => $validated['application_note'],
                'college_id' => $validated['college_id'],
                'course_id' => $validated['course_id']
            ]);

            // Record the activity
            Timeline::create([
                'lead_id' => $lead->id,
                'title' => 'Application Processed',
                'description' => "Application processed for lead: {$lead->name}, Application No: {$lead->id}, College: {$lead->college->name}, Course: {$lead->course->name}",
                'event_type' => 'manual',
                ...Timeline::performerAttributes(),
                'event_date' => now(),
            ]);

            ActivityLogger::log(
                "Processed application for lead {$lead->name} (Application No: {$lead->id})",
                'Application',
                auth()->guard('counselor')->user(),
                [
                    'lead' => $lead->id,
                    'application_no' => $lead->id,
                    'college' => $lead->college_id,
                    'course' => $lead->course_id
                ]
            );

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Application processed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to process application: ' . $e->getMessage());
        }
    }


    public function storeReservation(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'reservation_date' => 'required|date',
            'college_id' => 'required|exists:colleges,id',
            'course_id' => 'required|exists:courses,id',
            'reservation_note' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $lead = Lead::findOrFail($request->lead_id);
            
            // Update lead status and reservation details
            $lead->update([
                'status' => 'Reservation',
                'reservation_date' => $validated['reservation_date'],
                'college_id' => $validated['college_id'],
                'course_id' => $validated['course_id'],
                'reservation_note' => $validated['reservation_note']
            ]);

            // Record the activity
            Timeline::create([
                'lead_id' => $lead->id,
                'title' => 'Reservation Processed',
                'description' => "Reservation processed for lead: {$lead->name}, College: {$lead->college->name}, Course: {$lead->course->name}",
                'event_type' => 'manual',
                ...Timeline::performerAttributes(),
                'event_date' => now(),
            ]);

            ActivityLogger::log(
                "Processed reservation for lead {$lead->name} (Reservation No: {$lead->id})",
                'Reservation',
                auth()->guard('counselor')->user(),
                [
                    'lead' => $lead->id,
                    'college' => $lead->college_id,
                    'course' => $lead->course_id
                ]
            );

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Reservation processed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to process reservation: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'cancel_date' => 'required|date',
            'cancel_reason' => 'required|string',
            'cancel_note' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $lead = Lead::findOrFail($request->lead_id);
            
            // Update lead status and cancellation details
            $lead->update([
                'status' => 'Cancelled',
                'cancel_date' => $validated['cancel_date'],
                'cancel_reason' => $validated['cancel_reason'],
                'cancel_note' => $validated['cancel_note']
            ]);

            
            // Record the activity
            Timeline::create([
                'lead_id' => $lead->id,
                'title' => 'Lead Cancelled',
                'description' => "Lead cancelled: {$lead->name}, Reason: {$validated['cancel_reason']}",
                'event_type' => 'manual',
                ...Timeline::performerAttributes(),
                'event_date' => now(),
            ]);

            ActivityLogger::log(
                "Processed cancellation for lead {$lead->name} (Cancellation No: {$lead->id})",
                'Cancellation',
                auth()->guard('counselor')->user(),
                [
                    'lead' => $lead->id,
                    'college' => $lead->college_id,
                    'course' => $lead->course_id
                ]
            );

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Lead cancelled successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to cancel lead: ' . $e->getMessage());
        }
    }

    public function statusWiseLeads($status)
    {
        $leads = Lead::with(['source', 'course', 'college'])
            ->where('status', $status)
            ->where('counselor_id', auth()->guard('counselor')->user()->id)
            ->where('academic_year_id', $this->academicYear)
            ->orderBy('created_at', 'desc')
            ->get();
        $status = Str::title($status);
        return view('counselor.status-wise-leads', compact(
            'leads',
            'status'
        ));
    }

    public function bulkTransfer(Request $request)
    {
        $request->validate([
            'lead_ids' => 'required',
            'counselor_id' => 'required|exists:counselors,id',
            'next_fl_date' => 'required|date|after_or_equal:today',
            'transfer_note' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            
            $leadIds = json_decode($request->lead_ids);
            $successCount = 0;
            $toCounselor = Counselor::findOrFail($request->counselor_id);

            foreach ($leadIds as $leadId) {
                $lead = Lead::find($leadId);
                if ($lead) {
                    $fromCounselor = $lead->counselor;
                    $fromCounselorName = $fromCounselor ? $fromCounselor->name : 'Unassigned';

                    // Update lead
                    $lead->update([
                        'counselor_id' => $request->counselor_id,
                        'next_follow_up' => $request->next_fl_date,
                        'transfer_seen' => false
                    ]);

                    // Create transfer record
                    LeadTransfer::create([
                        'lead_id' => $lead->id,
                        'from_counselor_id' => $fromCounselor ? $fromCounselor->id : null,
                        'to_counselor_id' => $request->counselor_id,
                        'note' => $request->transfer_note,
                        'transferred_by' => auth()->guard('counselor')->user()->name
                    ]);

                    // Create timeline entry
                    Timeline::create([
                        'lead_id' => $lead->id,
                        'title' => 'Transferred Lead',
                        'description' => "Lead transferred to counselor: {$toCounselor->name}, from counselor: {$fromCounselorName}",
                        'event_type' => 'manual',
                        ...Timeline::performerAttributes(),
                        'event_date' => now(),
                    ]);

                    // Log activity
                    ActivityLogger::log(
                        "Transferred lead {$lead->name} to counselor {$toCounselor->name}",
                        'Transfer',
                        auth()->guard('counselor')->user(),
                        ['lead' => $lead->id, 'counselor' => $toCounselor->id]
                    );

                    $successCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$successCount} leads transferred successfully"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error transferring leads: ' . $e->getMessage()
            ], 500);
        }
    }
}