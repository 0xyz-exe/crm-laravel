<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crm_company_id')->nullable()->constrained('crm_companies')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('job_title')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_pipelines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('crm_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_pipeline_id')->constrained('crm_pipelines')->cascadeOnDelete();
            $table->string('name');
            $table->integer('order')->default(0);
            $table->integer('win_probability')->default(10); // 0-100%
            $table->timestamps();
        });

        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crm_company_id')->nullable()->constrained('crm_companies')->nullOnDelete();
            $table->foreignId('crm_contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('source')->default('website'); // website, referral, cold_call, social, advertisement
            $table->string('status')->default('new'); // new, contacted, qualified, unqualified
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crm_pipeline_id')->constrained('crm_pipelines')->cascadeOnDelete();
            $table->foreignId('crm_stage_id')->constrained('crm_stages')->cascadeOnDelete();
            $table->foreignId('crm_contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->foreignId('crm_company_id')->nullable()->constrained('crm_companies')->nullOnDelete();
            $table->string('title');
            $table->unsignedBigInteger('value_cents')->default(0);
            $table->date('expected_close_date')->nullable();
            $table->string('status')->default('open'); // open, won, lost
            $table->timestamps();
        });

        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crm_opportunity_id')->nullable()->constrained('crm_opportunities')->cascadeOnDelete();
            $table->foreignId('crm_lead_id')->nullable()->constrained('crm_leads')->cascadeOnDelete();
            $table->foreignId('crm_contact_id')->nullable()->constrained('crm_contacts')->cascadeOnDelete();
            $table->string('type'); // call, meeting, task, note
            $table->string('subject');
            $table->text('description')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('status')->default('pending'); // pending, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('crm_opportunities');
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('crm_stages');
        Schema::dropIfExists('crm_pipelines');
        Schema::dropIfExists('crm_contacts');
        Schema::dropIfExists('crm_companies');
    }
};
