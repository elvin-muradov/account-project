<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserTypesEnum;
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
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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
        'self_photo_files' => 'array'
    ];


    public function assignCompanies(array $companyIds): int
    {
        $accountantCompanies = $this->companiesServed;

        foreach ($accountantCompanies as $company) {
            $company->accountant_id = null;
            $company->accountant_assign_date = null;
            $company->save();
        }

        $companyIds = array_unique($companyIds);

        $selectedIndividualCompanies = Company::query()
            ->whereIn('id', $companyIds)
            ->where('owner_type', UserTypesEnum::INDIVIDUAL)
            ->whereNull('accountant_id')
            ->get();

        $selectedLegalCompanies = Company::query()
            ->whereIn('id', $companyIds)
            ->where('owner_type', UserTypesEnum::LEGAL)
            ->whereNull('accountant_id')
            ->get();

        $individualCompanies = $this->companiesServed()->where('owner_type', 'INDIVIDUAL')->get();
        $legalCompanies = $this->companiesServed()->where('owner_type', 'LEGAL')->get();

        if ($selectedIndividualCompanies->count() + $individualCompanies->count() <= 10) {
            foreach ($selectedIndividualCompanies as $company) {
                $company->accountant_id = $this->id;
                if ($company->accountant_assign_date == null) {
                    $company->accountant_assign_date = now();
                }
                $company->save();
            }
        } else {
            return 2;
        }

        if ($selectedLegalCompanies->count() + $legalCompanies->count() <= 5) {
            foreach ($selectedLegalCompanies as $company) {
                $company->accountant_id = $this->id;
                if ($company->accountant_assign_date == null) {
                    $company->accountant_assign_date = now();
                }
                $company->save();
            }
        } else {
            return 3;
        }

        return 1;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            foreach ($model->companiesServed as $company) {
                $company->accountant_assign_date = null;
                $company->save();
            }
        });
    }

    public function companyMain(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function createdEnvelopes(): HasMany
    {
        return $this->hasMany(Envelope::class, 'creator_id');
    }

    public function companiesServed(): HasMany
    {
        return $this->hasMany(Company::class, 'accountant_id');
    }
}
