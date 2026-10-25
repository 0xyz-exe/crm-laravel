<?php

namespace Tests\Feature;

use App\Models\CrmActivity;
use App\Models\CrmCompany;
use App\Models\CrmContact;
use App\Models\CrmLead;
use App\Models\CrmOpportunity;
use App\Models\CrmPipeline;
use App\Models\CrmStage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setupPipeline(): CrmPipeline
    {
        $pipeline = CrmPipeline::create(['name' => 'Standard Sales Pipeline', 'is_default' => true]);
        CrmStage::create(['crm_pipeline_id' => $pipeline->id, 'name' => 'Lead In', 'order' => 1, 'win_probability' => 10]);
        CrmStage::create(['crm_pipeline_id' => $pipeline->id, 'name' => 'Qualified', 'order' => 2, 'win_probability' => 30]);
        CrmStage::create(['crm_pipeline_id' => $pipeline->id, 'name' => 'Closed Won', 'order' => 5, 'win_probability' => 100]);
        CrmStage::create(['crm_pipeline_id' => $pipeline->id, 'name' => 'Closed Lost', 'order' => 6, 'win_probability' => 0]);
        return $pipeline;
    }

    public function test_crm_dashboard_renders_stages_and_metrics(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('CRM Sales & Deals Dashboard');
    }

    public function test_user_can_create_lead(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/leads', [
            'name' => 'Sarah Connor',
            'email' => 'sarah@cyberdyne.com',
            'phone' => '+15550199',
            'source' => 'website',
            'notes' => 'Looking for enterprise security plan.',
        ]);

        $response->assertRedirect('/leads');
        $this->assertDatabaseHas('crm_leads', [
            'name' => 'Sarah Connor',
            'email' => 'sarah@cyberdyne.com',
            'status' => 'new',
        ]);
    }

    public function test_user_can_convert_lead_to_deal_and_contact(): void
    {
        $pipeline = $this->setupPipeline();
        $user = User::factory()->create();

        $lead = $user->leads()->create([
            'name' => 'Miles Dyson',
            'email' => 'miles@research.com',
            'source' => 'referral',
            'status' => 'new',
        ]);

        $response = $this->actingAs($user)->post("/leads/{$lead->id}/convert", [
            'deal_title' => 'Neural Net Hardware Deployment',
            'deal_value' => 75000.00,
        ]);

        $response->assertRedirect('/opportunities');

        $lead->refresh();
        $this->assertEquals('qualified', $lead->status);

        $this->assertDatabaseHas('crm_contacts', [
            'email' => 'miles@research.com',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('crm_opportunities', [
            'title' => 'Neural Net Hardware Deployment',
            'value_cents' => 7500000,
            'status' => 'open',
            'user_id' => $user->id,
        ]);
    }

    public function test_opportunity_stage_move_and_win_status(): void
    {
        $pipeline = $this->setupPipeline();
        $leadInStage = $pipeline->stages()->where('name', 'Lead In')->first();
        $wonStage = $pipeline->stages()->where('name', 'Closed Won')->first();

        $user = User::factory()->create();

        $opp = $user->opportunities()->create([
            'crm_pipeline_id' => $pipeline->id,
            'crm_stage_id' => $leadInStage->id,
            'title' => 'Enterprise License Deal',
            'value_cents' => 2000000,
            'status' => 'open',
        ]);

        // Move to Closed Won
        $response = $this->actingAs($user)->post("/opportunities/{$opp->id}/move", [
            'crm_stage_id' => $wonStage->id,
        ]);

        $response->assertRedirect('/opportunities');
        $opp->refresh();
        $this->assertEquals($wonStage->id, $opp->crm_stage_id);
        $this->assertEquals('won', $opp->status);
    }

    public function test_user_cannot_move_another_users_opportunity(): void
    {
        $pipeline = $this->setupPipeline();
        $stage = $pipeline->stages()->first();

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $oppA = $userA->opportunities()->create([
            'crm_pipeline_id' => $pipeline->id,
            'crm_stage_id' => $stage->id,
            'title' => 'User A Deal',
            'value_cents' => 100000,
        ]);

        $response = $this->actingAs($userB)->post("/opportunities/{$oppA->id}/move", [
            'crm_stage_id' => $stage->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_contact_creation_and_csv_export(): void
    {
        $user = User::factory()->create();

        $user->contacts()->create([
            'first_name' => 'Alice',
            'last_name' => 'Wonderland',
            'email' => 'alice@wonderland.org',
            'phone' => '+44123456',
            'job_title' => 'Director of Strategy',
        ]);

        $response = $this->actingAs($user)->get('/contacts/export/csv');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Alice', $response->streamedContent());
        $this->assertStringContainsString('alice@wonderland.org', $response->streamedContent());
    }

    public function test_activity_scheduling_and_completion(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/activities', [
            'type' => 'call',
            'subject' => 'Product Demo with VP',
            'due_date' => now()->toDateString(),
            'description' => 'Cover analytics & API access',
        ]);

        $response->assertRedirect('/activities');
        $activity = CrmActivity::where('subject', 'Product Demo with VP')->firstOrFail();
        $this->assertEquals('pending', $activity->status);

        // Complete activity
        $completeResponse = $this->actingAs($user)->patch("/activities/{$activity->id}/complete");
        $completeResponse->assertSessionHas('success');

        $activity->refresh();
        $this->assertEquals('completed', $activity->status);
    }
}
