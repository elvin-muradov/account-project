<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class EnumsController extends Controller
{
    use HttpResponses;
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
}
