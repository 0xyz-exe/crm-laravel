<?php

namespace App\Http\Controllers;

use App\Models\CrmCompany;
use App\Models\CrmContact;
use App\Models\CrmOpportunity;
use App\Models\CrmPipeline;
use App\Models\CrmStage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OpportunityController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $pipeline = CrmPipeline::first();
        $stages = $pipeline->stages()->with(['opportunities' => fn ($q) => $q->where('user_id', $user->id)->with(['contact', 'company'])])->get();
        $contacts = CrmContact::where('user_id', $user->id)->orderBy('first_name')->get();
        $companies = CrmCompany::where('user_id', $user->id)->orderBy('name')->get();

        return view('opportunities.index', compact('pipeline', 'stages', 'contacts', 'companies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $pipeline = CrmPipeline::first();
        $firstStage = $pipeline->stages()->orderBy('order')->first();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'crm_stage_id' => 'nullable|exists:crm_stages,id',
            'crm_contact_id' => 'nullable|exists:crm_contacts,id',
            'crm_company_id' => 'nullable|exists:crm_companies,id',
            'expected_close_date' => 'nullable|date',
        ]);

        $request->user()->opportunities()->create([
            'crm_pipeline_id' => $pipeline->id,
            'crm_stage_id' => $validated['crm_stage_id'] ?? $firstStage->id,
            'crm_contact_id' => $validated['crm_contact_id'] ?? null,
            'crm_company_id' => $validated['crm_company_id'] ?? null,
            'title' => $validated['title'],
            'value_cents' => (int) round($validated['value'] * 100),
            'expected_close_date' => $validated['expected_close_date'] ?? null,
            'status' => 'open',
        ]);

        return redirect()->route('opportunities.index')->with('success', 'Opportunity created successfully.');
    }

    public function moveStage(Request $request, CrmOpportunity $opportunity): RedirectResponse
    {
        if ($opportunity->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'crm_stage_id' => 'required|exists:crm_stages,id',
        ]);

        $stage = CrmStage::findOrFail($validated['crm_stage_id']);
        $status = 'open';
        if (str_contains(strtolower($stage->name), 'won')) {
            $status = 'won';
        } elseif (str_contains(strtolower($stage->name), 'lost')) {
            $status = 'lost';
        }

        $opportunity->update([
            'crm_stage_id' => $stage->id,
            'status' => $status,
        ]);

        return redirect()->route('opportunities.index')->with('success', "Deal moved to {$stage->name}.");
    }
}
