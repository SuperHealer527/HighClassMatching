<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAccountActive
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && in_array($request->user()->status, ['rejected', 'suspended'], true)) {
            abort(403, 'このアカウントは現在利用できません。運営へお問い合わせください。');
        }

        return $next($request);
    }
}
