<?php declare(strict_types=1);

namespace App\Pipelines\ProcessSuite;

use App\Models\ApiSuite;
use App\Notifications\ApiSuiteTestReport;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Closure;

readonly class SendTestReport
{
    public function __construct(
        protected ApiSuite $apiSuite,
    ) {}

    public function handle(array $data, Closure $next): array
    {
        $user = FilamentUser::first();

        $user?->notify(
            new ApiSuiteTestReport(
                apiSuiteId: $this->apiSuite->id,
                apiSuiteName: $this->apiSuite->name,
                results: $data,
            ),
        );

        return $next($data);
    }
}
