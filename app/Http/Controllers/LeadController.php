<?php

namespace App\Http\Controllers;

use App\Models\CrmCompany;
use App\Models\CrmContact;
use App\Models\CrmLead;
use App\Models\CrmOpportunity;
use App\Models\CrmPipeline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $query = CrmLead::where('user_id', $request->user()->id)->latest();

        if ($source = $request->input('source')) {
            $query->where('source', $source);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $leads = $query->paginate(15);
        $companies = CrmCompany::where('user_id', $request->user()->id)->orderBy('name')->get();
        return view('leads.index', compact('leads', 'companies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'source' => 'required|in:website,referral,cold_call,social,advertisement',
            'crm_company_id' => 'nullable|exists:crm_companies,id',
            'notes' => 'nullable|string',
        ]);

        $request->user()->leads()->create($validated);

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function convert(Request $request, CrmLead $lead): RedirectResponse
    {
        if ($lead->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'deal_title' => 'required|string|max:255',
            'deal_value' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($lead, $validated, $request) {
            $pipeline = CrmPipeline::first();
            $firstStage = $pipeline->stages()->orderBy('order')->first();

            // Create Contact if not existing
            $contact = CrmContact::create([
                'user_id' => $request->user()->id,
                'crm_company_id' => $lead->crm_company_id,
                'first_name' => $lead->name,
                'last_name' => '(Lead Conversion)',
                'email' => $lead->email ?? 'converted@client.com',
                'phone' => $lead->phone,
            ]);

            // Create Opportunity
            CrmOpportunity::create([
                'user_id' => $request->user()->id,
                'crm_pipeline_id' => $pipeline->id,
                'crm_stage_id' => $firstStage->id,
                'crm_contact_id' => $contact->id,
                'crm_company_id' => $lead->crm_company_id,
                'title' => $validated['deal_title'],
                'value_cents' => (int) round($validated['deal_value'] * 100),
                'status' => 'open',
            ]);

            $lead->update(['status' => 'qualified']);
        });

        return redirect()->route('opportunities.index')->with('success', "Lead converted into deal {$validated['deal_title']}.");
    }
}
