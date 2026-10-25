<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public function opportunities() { return $this->hasMany(CrmOpportunity::class); }
    public function leads() { return $this->hasMany(CrmLead::class); }
    public function contacts() { return $this->hasMany(CrmContact::class); }
    public function companies() { return $this->hasMany(CrmCompany::class); }
    public function activities() { return $this->hasMany(CrmActivity::class); }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
}
