<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCompanyID
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "İcazəniz yoxdur", code: 403);
        }

        return $next($request);
    }
}
