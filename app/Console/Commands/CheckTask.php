<?php

namespace App\Console\Commands;

use App\Models\Company\Company;
use App\Models\Employee;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check tasks for accountants';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $ydmExpiredCompanies = Company::query()
            ->with(['accountant'])
            ->whereNotNull('accountant_id')
            ->where('ydm_card_expired_at', '<', Carbon::today())
            ->get();

        $asanSignExpiredCompanies = Company::query()
            ->with(['accountant'])
            ->whereNotNull('accountant_id')
            ->where('asan_sign_expired_at', '<', Carbon::today())
            ->get();

        $salaryCardExpiredEmployees = Employee::query()
            ->with(['company.accountant'])
            ->whereHas('company', function ($query) {
                $query->whereNotNull('accountant_id');
            })
            ->where('salary_card_expired_at', '<', Carbon::today())
            ->get();

        foreach ($ydmExpiredCompanies as $company) {
            Task::query()->create([
                'type' => 'COMPANY',
                'subtype' => 'YDM_CARD',
                'title' => 'YDM kartının müddətinin bitməsi',
                'description' => $company->company_name . ' adlı şirkətin YDM kartının bitməsi ilə bağlı tapşırıq.',
                'company_id' => $company->id,
                'accountant_id' => $company->accountant?->id,
            ]);
        }

        foreach ($asanSignExpiredCompanies as $company) {
            Task::query()->create([
                'type' => 'COMPANY',
                'subtype' => 'ASAN_SIGN',
                'title' => 'ASAN imza vaxtının bitməsi',
                'description' => $company->company_name .
                    ' adlı şirkətin ASAN imza vaxtının bitməsi ilə bağlı tapşırıq.',
                'company_id' => $company->id,
                'accountant_id' => $company->accountant?->id,
            ]);
        }

        foreach ($salaryCardExpiredEmployees as $employee) {
            Task::query()->create([
                'type' => 'EMPLOYEE',
                'subtype' => 'SALARY_CARD',
                'title' => 'Maaş kartının vaxtının bitməsi',
                'description' => $employee->company->company_name . ' şirkətinin ' . $employee->name . ' '
                    . $employee->surname . ' adlı işçisinin maaş kartının vaxtının bitməsi ilə bağlı tapşırıq.',
                'company_id' => $employee->company_id,
                'employee_id' => $employee->id,
                'accountant_id' => $employee->company?->accountant?->id,
            ]);
        }
    }
}
