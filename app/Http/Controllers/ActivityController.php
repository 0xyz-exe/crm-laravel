<?php

namespace App\Http\Controllers;

use App\Models\CrmActivity;
use App\Models\CrmOpportunity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $activities = CrmActivity::where('user_id', $user->id)->with(['opportunity', 'lead', 'contact'])->latest()->paginate(15);
        $opportunities = CrmOpportunity::where('user_id', $user->id)->orderBy('title')->get();

        return view('activities.index', compact('activities', 'opportunities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:call,meeting,task,note',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'crm_opportunity_id' => 'nullable|exists:crm_opportunities,id',
        ]);

        $request->user()->activities()->create($validated);

        return redirect()->route('activities.index')->with('success', 'Activity scheduled.');
    }

    public function complete(Request $request, CrmActivity $activity): RedirectResponse
    {
        if ($activity->user_id !== $request->user()->id) {
            abort(403);
        }

        $activity->update(['status' => 'completed']);

        return back()->with('success', 'Activity marked as completed.');
    }
}
