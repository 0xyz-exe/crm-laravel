<?php

namespace App\Http\Controllers;

use App\Models\CrmCompany;
use App\Models\CrmContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $contacts = CrmContact::where('user_id', $user->id)->with('company')->latest()->paginate(15);
        $companies = CrmCompany::where('user_id', $user->id)->withCount('contacts')->latest()->get();

        return view('contacts.index', compact('contacts', 'companies'));
    }

    public function storeContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'crm_company_id' => 'nullable|exists:crm_companies,id',
        ]);

        $request->user()->contacts()->create($validated);

        return redirect()->route('contacts.index')->with('success', 'Contact created.');
    }

    public function storeCompany(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $request->user()->companies()->create($validated);

        return redirect()->route('contacts.index')->with('success', 'Company created.');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = $request->user();
        $contacts = CrmContact::where('user_id', $user->id)->with('company')->get();

        $response = new StreamedResponse(function () use ($contacts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Job Title', 'Company']);

            foreach ($contacts as $contact) {
                fputcsv($handle, [
                    $contact->id,
                    $contact->first_name,
                    $contact->last_name,
                    $contact->email,
                    $contact->phone ?? '',
                    $contact->job_title ?? '',
                    $contact->company->name ?? '',
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="crm_contacts_' . date('Y-m-d') . '.csv"');

        return $response;
    }
}
