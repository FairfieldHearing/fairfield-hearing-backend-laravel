<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddAgentLinkHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if (method_exists($response, 'header')) {
            $response->header('Link', '</.well-known/api-catalog>; rel="api-catalog"');
        }

        return $response;
    }
}
