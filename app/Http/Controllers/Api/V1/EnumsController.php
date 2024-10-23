<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\EducationTypesEnum;
use App\Enums\EmployeeTypes;
use App\Enums\GenderTypes;
use App\Http\Controllers\Controller;

class EnumsController extends Controller
{
    public function companyCategories(): array
    {
        return [
            [
                'value' => 'MICRO',
                'label' => trans('company_categories.MICRO')
            ],
            [
                'value' => 'JUNIOR',
                'label' => trans('company_categories.JUNIOR')
            ],
            [
                'value' => 'MIDDLE',
                'label' => trans('company_categories.MIDDLE')
            ],
            [
                'value' => 'SENIOR',
                'label' => trans('company_categories.SENIOR')
            ],
        ];
    }

    public function transportTypes(): array
    {
        return [
            [
                'FCA' => 'FCA',
                'label' => trans('transport_types.FCA')
            ],
            [
                'FOB' => 'FOB',
                'label' => trans('transport_types.FOB')
            ],
            [
                'CIF' => 'CIF',
                'label' => trans('transport_types.CIF')
            ],
            [
                'EXW' => 'EXW',
                'label' => trans('transport_types.EXW')
            ],
            [
                'DAP' => 'DAP',
                'label' => trans('transport_types.DAP')
            ],
            [
                'DAT' => 'DAT',
                'label' => trans('transport_types.DAT')
            ],
            [
                'CPT' => 'CPT',
                'label' => trans('transport_types.CPT')
            ],
            [
                'CIP' => 'CIP',
                'label' => trans('transport_types.CIP')
            ],
            [
                'DDP' => 'DDP',
                'label' => trans('transport_types.DDP')
            ],
            [
                'FAS' => 'FAS',
                'label' => trans('transport_types.FAS')
            ],
            [
                'CFR' => 'CFR',
                'label' => trans('transport_types.CFR')
            ]
        ];
    }

    public function educationTypes(): array
    {
        $types = EducationTypesEnum::toArray();

        return array_map(function ($type) {
            return [
                'value' => $type,
                'label' => trans('education_types.' . $type)
            ];
        }, $types);
    }

    public function genderTypes(): array
    {
        $types = GenderTypes::toArray();

        return array_map(function ($type) {
            return [
                'value' => $type,
                'label' => trans('genders.' . $type)
            ];
        }, $types);
    }

    public function employeeTypes(): array
    {
        $types = EmployeeTypes::toArray();

        return array_map(function ($type) {
            return [
                'value' => $type,
                'label' => trans('employee_types.' . $type)
            ];
        }, $types);
    }
}
