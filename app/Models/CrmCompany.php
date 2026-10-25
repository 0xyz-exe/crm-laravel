<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmCompany extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CrmContact::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(CrmOpportunity::class);
    }
}
