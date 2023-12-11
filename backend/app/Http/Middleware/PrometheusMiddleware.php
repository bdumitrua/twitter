<?php

namespace App\Http\Middleware;

use App\Helpers\Stopwatch;
use App\Prometheus\PrometheusService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrometheusMiddleware
{
    protected $prometheusService;
    protected $stopwatch;

    /**
     * @param PrometheusService $prometheusService
     */
    public function __construct(PrometheusService $prometheusService)
    {
        $this->prometheusService = $prometheusService;
        $this->stopwatch = new Stopwatch();
    }

    /**
     * @param Request $request
     * @param Closure $next
     * 
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()->getName();
        $this->prometheusService->incrementRequestCounter($routeName);
        $this->stopwatch->start();

        $response = $next($request);

        $duration = $this->stopwatch->stop();
        $this->prometheusService->addResponseTimeHistogram($duration, $routeName);

        return $response;
    }
}
