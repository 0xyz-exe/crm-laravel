<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmPipeline extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function stages(): HasMany
    {
        return $this->hasMany(CrmStage::class)->orderBy('order');
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(CrmOpportunity::class);
    }
}
