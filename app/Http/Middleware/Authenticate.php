<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        // Supaya API tidak redirect ke route login yang tidak kamu punya
        return $request->expectsJson() ? null : null;
    }
}