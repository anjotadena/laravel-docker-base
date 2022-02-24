<?php

namespace App\Http\Middleware;

use App\Traits\JsonResponser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiJSONRequest
{
    use JsonResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Content-Type') !== 'application/json') {
            return $this->messageResponse(__('API accepts json request.'), Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
