<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = \App\Models\User::firstOrCreate(['email' => 'alex@crmplatform.io'], [
            'name' => 'Alex Mercer',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        $pipeline = \App\Models\CrmPipeline::firstOrCreate(['name' => 'Standard Sales Pipeline'], ['is_default' => true]);

        $stageLeadIn = \App\Models\CrmStage::firstOrCreate(['crm_pipeline_id' => $pipeline->id, 'name' => 'Lead In'], ['order' => 1, 'win_probability' => 10]);
        $stageQualified = \App\Models\CrmStage::firstOrCreate(['crm_pipeline_id' => $pipeline->id, 'name' => 'Qualified'], ['order' => 2, 'win_probability' => 30]);
        $stageProposal = \App\Models\CrmStage::firstOrCreate(['crm_pipeline_id' => $pipeline->id, 'name' => 'Proposal Sent'], ['order' => 3, 'win_probability' => 60]);
        $stageNegotiation = \App\Models\CrmStage::firstOrCreate(['crm_pipeline_id' => $pipeline->id, 'name' => 'Negotiation'], ['order' => 4, 'win_probability' => 80]);
        $stageWon = \App\Models\CrmStage::firstOrCreate(['crm_pipeline_id' => $pipeline->id, 'name' => 'Closed Won'], ['order' => 5, 'win_probability' => 100]);
        $stageLost = \App\Models\CrmStage::firstOrCreate(['crm_pipeline_id' => $pipeline->id, 'name' => 'Closed Lost'], ['order' => 6, 'win_probability' => 0]);

        $c1 = \App\Models\CrmCompany::firstOrCreate(['user_id' => $user->id, 'name' => 'Apex Dynamics'], ['industry' => 'Enterprise Software', 'website' => 'https://apexdynamics.tech', 'phone' => '+1 (555) 234-5678', 'address' => '100 Silicon Blvd, Suite 400, San Jose, CA']);
        $c2 = \App\Models\CrmCompany::firstOrCreate(['user_id' => $user->id, 'name' => 'Horizon Cloud Solutions'], ['industry' => 'Cloud Infrastructure', 'website' => 'https://horizoncloud.io', 'phone' => '+1 (555) 876-5432', 'address' => '450 Pine St, Floor 12, Seattle, WA']);
        $c3 = \App\Models\CrmCompany::firstOrCreate(['user_id' => $user->id, 'name' => 'Nexus Retail Group'], ['industry' => 'E-Commerce & Retail', 'website' => 'https://nexusretail.com', 'phone' => '+1 (555) 345-6789', 'address' => '789 Broadway Ave, New York, NY']);
        $c4 = \App\Models\CrmCompany::firstOrCreate(['user_id' => $user->id, 'name' => 'CyberGuard Security'], ['industry' => 'Cybersecurity', 'website' => 'https://cyberguard.sec', 'phone' => '+1 (555) 987-6543', 'address' => '220 Defense Way, Austin, TX']);

        $ct1 = \App\Models\CrmContact::firstOrCreate(['user_id' => $user->id, 'email' => 'miles.dyson@apexdynamics.tech'], ['crm_company_id' => $c1->id, 'first_name' => 'Miles', 'last_name' => 'Dyson', 'phone' => '+1 (555) 234-5679', 'job_title' => 'Chief Technology Officer']);
        $ct2 = \App\Models\CrmContact::firstOrCreate(['user_id' => $user->id, 'email' => 'elena.rostova@horizoncloud.io'], ['crm_company_id' => $c2->id, 'first_name' => 'Elena', 'last_name' => 'Rostova', 'phone' => '+1 (555) 876-5433', 'job_title' => 'Director of Procurement']);
        $ct3 = \App\Models\CrmContact::firstOrCreate(['user_id' => $user->id, 'email' => 'sophia.chen@nexusretail.com'], ['crm_company_id' => $c3->id, 'first_name' => 'Sophia', 'last_name' => 'Chen', 'phone' => '+1 (555) 345-6790', 'job_title' => 'Head of Digital Transformation']);
        $ct4 = \App\Models\CrmContact::firstOrCreate(['user_id' => $user->id, 'email' => 'sarah.connor@cyberguard.sec'], ['crm_company_id' => $c4->id, 'first_name' => 'Sarah', 'last_name' => 'Connor', 'phone' => '+1 (555) 987-6544', 'job_title' => 'VP Information Security']);

        \App\Models\CrmLead::firstOrCreate(['user_id' => $user->id, 'email' => 'jordan.blake@solarisenergy.com'], ['name' => 'Jordan Blake', 'phone' => '+1 (555) 432-1098', 'source' => 'website', 'status' => 'new', 'notes' => 'Inquired about enterprise CRM custom integrations.']);
        \App\Models\CrmLead::firstOrCreate(['user_id' => $user->id, 'email' => 'claire.redfield@umbrella-bio.com'], ['name' => 'Claire Redfield', 'phone' => '+1 (555) 654-3210', 'source' => 'referral', 'status' => 'contacted', 'notes' => 'Looking to migrate 40 sales reps from legacy spreadsheet system.']);
        \App\Models\CrmLead::firstOrCreate(['user_id' => $user->id, 'email' => 'ethan.hunt@imf-global.org'], ['name' => 'Ethan Hunt', 'phone' => '+1 (555) 789-0123', 'source' => 'social', 'status' => 'qualified', 'notes' => 'Fast-track trial requested for APAC operations team.']);
        \App\Models\CrmLead::firstOrCreate(['user_id' => $user->id, 'email' => 'marcus.vance@vantagemedia.com'], ['name' => 'Marcus Vance', 'phone' => '+1 (555) 210-9876', 'source' => 'cold_call', 'status' => 'new', 'notes' => 'Follow up next Tuesday on digital pipeline metrics.']);

        \App\Models\CrmOpportunity::firstOrCreate(['user_id' => $user->id, 'title' => 'AI Customer Copilot Integration'], [
            'crm_pipeline_id' => $pipeline->id,
            'crm_stage_id' => $stageLeadIn->id,
            'crm_contact_id' => $ct1->id,
            'crm_company_id' => $c1->id,
            'value_cents' => 3800000,
            'expected_close_date' => now()->addDays(45)->toDateString(),
            'status' => 'open',
        ]);

        \App\Models\CrmOpportunity::firstOrCreate(['user_id' => $user->id, 'title' => 'Omnichannel POS Sync Platform'], [
            'crm_pipeline_id' => $pipeline->id,
            'crm_stage_id' => $stageQualified->id,
            'crm_contact_id' => $ct3->id,
            'crm_company_id' => $c3->id,
            'value_cents' => 6400000,
            'expected_close_date' => now()->addDays(30)->toDateString(),
            'status' => 'open',
        ]);

        \App\Models\CrmOpportunity::firstOrCreate(['user_id' => $user->id, 'title' => 'Enterprise Cloud Migration Package'], [
            'crm_pipeline_id' => $pipeline->id,
            'crm_stage_id' => $stageProposal->id,
            'crm_contact_id' => $ct2->id,
            'crm_company_id' => $c2->id,
            'value_cents' => 12500000,
            'expected_close_date' => now()->addDays(20)->toDateString(),
            'status' => 'open',
        ]);

        \App\Models\CrmOpportunity::firstOrCreate(['user_id' => $user->id, 'title' => 'CyberDefense Multi-Cloud Guard'], [
            'crm_pipeline_id' => $pipeline->id,
            'crm_stage_id' => $stageNegotiation->id,
            'crm_contact_id' => $ct4->id,
            'crm_company_id' => $c4->id,
            'value_cents' => 18000000,
            'expected_close_date' => now()->addDays(10)->toDateString(),
            'status' => 'open',
        ]);

        \App\Models\CrmOpportunity::firstOrCreate(['user_id' => $user->id, 'title' => 'Global Infrastructure SLA Contract'], [
            'crm_pipeline_id' => $pipeline->id,
            'crm_stage_id' => $stageWon->id,
            'crm_contact_id' => $ct2->id,
            'crm_company_id' => $c2->id,
            'value_cents' => 9500000,
            'expected_close_date' => now()->subDays(5)->toDateString(),
            'status' => 'won',
        ]);

        \App\Models\CrmOpportunity::firstOrCreate(['user_id' => $user->id, 'title' => 'Legacy On-Prem CRM Bridge'], [
            'crm_pipeline_id' => $pipeline->id,
            'crm_stage_id' => $stageLost->id,
            'crm_contact_id' => $ct1->id,
            'crm_company_id' => $c1->id,
            'value_cents' => 2200000,
            'expected_close_date' => now()->subDays(12)->toDateString(),
            'status' => 'lost',
        ]);

        \App\Models\CrmActivity::firstOrCreate(['user_id' => $user->id, 'subject' => 'Executive Demo & Scope Alignment'], [
            'type' => 'meeting',
            'description' => 'Review multi-region tenant requirements with procurement engineering team.',
            'due_date' => now()->addDays(2)->setHour(14)->setMinute(30),
            'status' => 'pending',
            'crm_contact_id' => $ct2->id,
        ]);

        \App\Models\CrmActivity::firstOrCreate(['user_id' => $user->id, 'subject' => 'Follow up on Security Compliance SLA'], [
            'type' => 'call',
            'description' => 'Discuss SOC2 type II audit matrix and ISO 27001 addendum.',
            'due_date' => now()->addDays(1)->setHour(10)->setMinute(0),
            'status' => 'pending',
            'crm_contact_id' => $ct4->id,
        ]);

        \App\Models\CrmActivity::firstOrCreate(['user_id' => $user->id, 'subject' => 'Send Revised Proposal & Pricing Tier'], [
            'type' => 'task',
            'description' => 'Apply 10% annual multi-seat discount and send contract link.',
            'due_date' => now()->addDays(3)->setHour(16)->setMinute(0),
            'status' => 'pending',
            'crm_contact_id' => $ct3->id,
        ]);

        \App\Models\CrmActivity::firstOrCreate(['user_id' => $user->id, 'subject' => 'Quarterly Review Discovery Notes'], [
            'type' => 'note',
            'description' => 'Customer confirmed budget signoff for Q4 expansion phase.',
            'due_date' => now()->subDays(1)->setHour(11)->setMinute(0),
            'status' => 'completed',
            'crm_contact_id' => $ct1->id,
        ]);
    }
}
