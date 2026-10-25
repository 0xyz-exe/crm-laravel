<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CrmDashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [CrmDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Leads
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

    // Deals / Opportunities
    Route::get('/opportunities', [OpportunityController::class, 'index'])->name('opportunities.index');
    Route::post('/opportunities', [OpportunityController::class, 'store'])->name('opportunities.store');
    Route::post('/opportunities/{opportunity}/move', [OpportunityController::class, 'moveStage'])->name('opportunities.move');

    // Contacts & Companies
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts', [ContactController::class, 'storeContact'])->name('contacts.store');
    Route::post('/companies', [ContactController::class, 'storeCompany'])->name('companies.store');
    Route::get('/contacts/export/csv', [ContactController::class, 'exportCsv'])->name('contacts.export');

    // Activities
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::patch('/activities/{activity}/complete', [ActivityController::class, 'complete'])->name('activities.complete');
});

require __DIR__.'/auth.php';
