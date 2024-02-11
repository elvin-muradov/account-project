<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        $companies = Company::query()->count();
        $user = User::query()
            ->with('companies')
            ->where('id', '=', 2)
            ->first();


        dd($companies, $user->companies);
    }
}
