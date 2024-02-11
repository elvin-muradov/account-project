<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Company\Company;
use App\Models\Envelopes\Envelope;
use App\Traits\HasCompaniesServed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasCompaniesServed;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'father_name',
        'phone',
        'username',
        'birth_date',
        'education',
        'education_files',
        'certificate_files',
        'cv_files',
        'self_photo_files',
        'previous_job',
        'account_status',
        'email',
        'password',
        'last_login_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'education_files' => 'array',
        'certificate_files' => 'array',
        'cv_files' => 'array',
        'self_photo_files' => 'array',
        'birth_date' => 'datetime',
    ];

    public function assignCompanies(array $companyIds)
    {
        $individualCompanyCount = $this->companiesServed()->where('type', 'INDIVIDUAL')->count();
        $legalCompanyCount = $this->companiesServed()->where('type', 'LEGAL')->count();


    }

    public function companyMain(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function sentEnvelopes(): HasMany
    {
        return $this->hasMany(Envelope::class, 'sender_id');
    }

    public function companiesServed(): HasMany
    {
        return $this->hasMany(Company::class, 'accountant_id');
    }
}
