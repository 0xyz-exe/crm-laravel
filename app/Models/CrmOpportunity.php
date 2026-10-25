<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmOpportunity extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'value_cents' => 'integer',
        'expected_close_date' => 'date',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(CrmPipeline::class, 'crm_pipeline_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(CrmStage::class, 'crm_stage_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'crm_contact_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CrmCompany::class, 'crm_company_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class);
    }
}
