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

        if (!$authCheck) {
            return $this->error(message: "Giriş edin", code: 403);
        }

        if (auth()->user()->hasRole(['accountant'])) {
            $userServedCompanies = auth()->user()->load('companiesServed')->companiesServed()->pluck('id')->toArray();

            if ($request->input('company_id') && !in_array($request->input('company_id'), $userServedCompanies)) {
                return $this->error(message: "Sizin bu şirkətə xidmət göstərmək hüququnuz yoxdur", code: 403);
            }
        }

        return $next($request);
    }
}
