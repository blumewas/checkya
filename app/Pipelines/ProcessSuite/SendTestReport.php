<?php declare(strict_types=1);

namespace App\Pipelines\ProcessSuite;

use App\Data\TestResult;
use App\Models\ApiSuite;
use App\Notifications\ApiSuiteTestReport;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Closure;

readonly class SendTestReport
{
    public function __construct(
        protected ApiSuite $apiSuite,
    ) {}

    public function handle(TestResult $results, Closure $next): TestResult
    {
        $user = FilamentUser::first();

        $user?->notify(
            new ApiSuiteTestReport(
                apiSuiteId: $this->apiSuite->id,
                apiSuiteName: $this->apiSuite->name,
                results: $results,
            ),
        );

        return $next($results);
    }
}
