<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserRoleCheckingForServedCompanies
{
    use HttpResponses;

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authCheck = auth()->check();

        if ($authCheck && auth()->user()->hasRole('accountant')) {
            $authUser = auth()->user();
            $userServedCompanies = $authUser->companiesServed()->pluck('id')->toArray();

            if ($request->input('company_id') && !in_array($request->input('company_id'), $userServedCompanies)) {
                return $this->error(message: "Siz bu şirkətə xidmət göstərmirsiniz", code: 403);
            }
        }

        return $next($request);
    }
}
