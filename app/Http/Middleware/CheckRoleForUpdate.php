<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponses;
use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleForUpdate
{
    use HttpResponses;

    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, $model): Response|JsonResponse
    {
        $user = $request->user('user');

        $createdAt = $model::find($request->route('envelope'))->created_at;
        $afterTwoMonths = Carbon::make($createdAt)->addSeconds(2);

        $check = $afterTwoMonths > now();

        if ($user->hasRole('accountant') && $check) {
            return $next($request);
        }

        if ($user->hasAnyRole('leading_expert', 'department_head')) {
            return $next($request);
        }

        return $this->error(message: "İcazə yoxdur", code: 403);
    }
}
