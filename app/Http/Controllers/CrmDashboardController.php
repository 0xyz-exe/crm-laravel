<?php

namespace App\Http\Controllers;

use App\Models\CrmActivity;
use App\Models\CrmLead;
use App\Models\CrmOpportunity;
use App\Models\CrmPipeline;
use App\Models\CrmStage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Default pipeline seeding if empty
        $pipeline = CrmPipeline::first();
        if (!$pipeline) {
            $pipeline = CrmPipeline::create(['name' => 'Standard Sales Pipeline', 'is_default' => true]);
            CrmStage::create(['crm_pipeline_id' => $pipeline->id, 'name' => 'Lead In', 'order' => 1, 'win_probability' => 10]);
            CrmStage::create(['crm_pipeline_id' => $pipeline->id, 'name' => 'Qualified', 'order' => 2, 'win_probability' => 30]);
            CrmStage::create(['crm_pipeline_id' => $pipeline->id, 'name' => 'Proposal Sent', 'order' => 3, 'win_probability' => 60]);
            CrmStage::create(['crm_pipeline_id' => $pipeline->id, 'name' => 'Negotiation', 'order' => 4, 'win_probability' => 80]);
            CrmStage::create(['crm_pipeline_id' => $pipeline->id, 'name' => 'Closed Won', 'order' => 5, 'win_probability' => 100]);
            CrmStage::create(['crm_pipeline_id' => $pipeline->id, 'name' => 'Closed Lost', 'order' => 6, 'win_probability' => 0]);
        }

        $stages = $pipeline->stages()->with(['opportunities' => fn ($q) => $q->where('user_id', $user->id)->with(['contact', 'company'])])->get();

        $userOpps = CrmOpportunity::where('user_id', $user->id);
        $totalPipelineValue = (int) (clone $userOpps)->where('status', 'open')->sum('value_cents');
        $wonValue = (int) (clone $userOpps)->where('status', 'won')->sum('value_cents');

        $metrics = [
            'total_leads' => CrmLead::where('user_id', $user->id)->count(),
            'open_opportunities' => (clone $userOpps)->where('status', 'open')->count(),
            'pipeline_value_cents' => $totalPipelineValue,
            'won_value_cents' => $wonValue,
            'pending_activities_count' => CrmActivity::where('user_id', $user->id)->where('status', 'pending')->count(),
        ];

        $pendingActivities = CrmActivity::where('user_id', $user->id)->where('status', 'pending')->latest()->take(5)->get();

        return view('dashboard', compact('pipeline', 'stages', 'metrics', 'pendingActivities'));
    }
}
