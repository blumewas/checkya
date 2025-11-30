<?php declare(strict_types=1);

namespace App\Pipelines;

use App\Data\TestResult;
use App\Enums\ApiSuiteStatusEnum;
use App\Models\ApiSuite;
use App\Notifications\ApiSuiteTestFailed;
use App\Pipelines\ProcessSuite\SendApiRequest;
use App\Pipelines\ProcessSuite\SendTestReport;
use App\Util\ApiSuiteClient;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessSuitePipeline extends Pipeline
{
    private ApiSuite $apiSuite;

    public static function run(ApiSuite $apiSuite): ?TestResult
    {
        $requestSteps = array_map(fn ($request): SendApiRequest => app(SendApiRequest::class, [
            'client' => ApiSuiteClient::make($apiSuite),
            'apiSuite' => $apiSuite,
            'request' => $request,
        ]), $apiSuite->requests);

        $memorized = new TestResult;

        return app(static::class)
            ->setSuite($apiSuite)
            ->through([...$requestSteps, new SendTestReport($apiSuite)])
            ->send($memorized)
            ->thenReturn();
    }

    public function setSuite(ApiSuite $apiSuite): self
    {
        $this->apiSuite = $apiSuite;

        return $this;
    }

    /**
     * Handle the given exception.
     *
     * @param mixed $passable
     *
     * @return mixed
     *
     * @throws Throwable
     */
    protected function handleException($passable, Throwable $e)
    {
        $this->apiSuite->update([
            'status' => ApiSuiteStatusEnum::Error,
        ]);

        Log::error('An error occured running test suite: '.$this->apiSuite->id, [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        $user = FilamentUser::first();

        $user?->notify(
            new ApiSuiteTestFailed(
                apiSuiteId: $this->apiSuite->id,
                apiSuiteName: $this->apiSuite->name,
                errorMessage: $e->getMessage(),
            ),
        );
        // throw $e;
    }
}
