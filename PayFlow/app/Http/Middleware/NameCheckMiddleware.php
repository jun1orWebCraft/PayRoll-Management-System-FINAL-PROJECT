<?php

namespace App\Http\Middleware;
use App\Models\User;
use App\Http\Controllers\PayrollController;

use Closure;
use Illuminate\Http\Request;

class NameCheckMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user() || $request->user()->role !== $role) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
