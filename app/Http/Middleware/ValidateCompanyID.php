<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCompanyID
{
    use HttpResponses;

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $companyId = $request->hasHeader('company-id') ? $request->header('company-id') : null;

        if (!empty($companyId)) {
            return $next($request);
        }

        return $this->error(message: "Şirkət identifikatorı yoxdur", code: 400);
    }
}
