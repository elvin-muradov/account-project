<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpertHeadMiddleware
{
    use HttpResponses;

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() &&
            (auth()->user()->hasRole('leading_expert') ||
                auth()->user()->hasRole('department_head'))) {
            return $next($request);
        } else {
            return $this->error(message: "İcazəniz yoxdur", code: 403);
        }
    }
}
