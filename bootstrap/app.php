<?php declare(strict_types=1);

use App\Enums\ApiSuiteStatusEnum;
use App\Models\ApiSuite;
use App\Pipelines\ProcessSuitePipeline;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
    })
    ->withSchedule(function (Schedule $schedule): void {
        try {
            $suites = ApiSuite::query()
                ->whereStatus(ApiSuiteStatusEnum::Active)
                ->get();
        } catch (Throwable $th) {
            Log::error('Cant run schedules. Could not get api suites');

            return;
        }

        $suites
            ->each(function (ApiSuite $apiSuite) use ($schedule): void {
                // Add entry to schedule
                $schedule->cron($apiSuite->cron_schedule->value)
                    ->call(function () use ($apiSuite): void {
                        $result = ProcessSuitePipeline::run($apiSuite);

                        Log::info('');
                    });
            });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);
    })->create();
