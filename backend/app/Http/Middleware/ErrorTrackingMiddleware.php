<?php

namespace App\Http\Middleware;

use App\Prometheus\PrometheusService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorTrackingMiddleware
{
    protected $prometheusService;

    public function __construct(PrometheusService $prometheusService)
    {
        $this->prometheusService = $prometheusService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->isClientError() || $response->isServerError()) {
            $routeName = $request->route()->getName();
            $this->prometheusService->incrementErrorCounter($response->getStatusCode(), $routeName);
        }

        return $response;
    }
}
