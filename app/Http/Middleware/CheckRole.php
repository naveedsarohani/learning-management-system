<?php

namespace App\Http\Middleware;

use App\Http\Utils\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Utils\Status;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        if (!$user = Auth::user()) {
            return $this->errorResponse(Status::UNAUTHORIZED, 'the request was unauthorized');
        }

        if (!in_array($user->role, ['admin', 'instructor'])) {
            return $this->errorResponse(Status::FORBIDDEN, 'the request was forbidden');
        }

        return $next($request);
    }
}
