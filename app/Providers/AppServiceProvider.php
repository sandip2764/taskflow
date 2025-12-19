<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        // Rate limiter for authentication endpoints
        // Stricter limit to prevent brute force attacks
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many authentication attempts. Please try again later.',
                        'retry_after_seconds' => $headers['Retry-After'] ?? 60,
                    ], 429);
                });
        });

        // Rate limiter for general API endpoints
        // Per-user limit (by user ID if authenticated, otherwise by IP)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many requests. Please slow down.',
                        'retry_after_seconds' => $headers['Retry-After'] ?? 60,
                    ], 429);
                });
        });

        // Rate limiter for resource-intensive operations
        // Lower limit for expensive queries like statistics
        RateLimiter::for('heavy', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many heavy requests. This endpoint is rate-limited to 10 requests per minute.',
                        'retry_after_seconds' => $headers['Retry-After'] ?? 60,
                    ], 429);
                });
        });
    }
}
