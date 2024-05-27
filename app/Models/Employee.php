<?php

namespace App\Models;

use App\Models\Company\AttendanceLog;
use App\Models\Company\Company;
use App\Models\Company\Position;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'remember_token'
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

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'employee_id');
    }
}
