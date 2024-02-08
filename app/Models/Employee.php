<?php

namespace App\Models;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guarded = [];

    protected $casts = [
        'salary' => 'float',
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function companiesAsDirector(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'director_id');
    }

    public function companiesAsMainUser(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'main_user_id');
    }
}
