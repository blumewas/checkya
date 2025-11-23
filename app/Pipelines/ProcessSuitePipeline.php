<?php declare(strict_types=1);

namespace App\Pipelines;

use App\Enums\ApiSuiteStatusEnum;
use App\Models\ApiSuite;
use App\Pipelines\ProcessSuite\SendApiRequest;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Pipeline\Pipeline;
use Throwable;

class ProcessSuitePipeline extends Pipeline
{
    private ApiSuite $apiSuite;

    public static function run(ApiSuite $apiSuite): array
    {
        $client = app(HttpFactory::class)
            ->createPendingRequest();

        foreach ($apiSuite->client_config as $method => $value) {
            $client->when(
                method_exists($client, $method) && ! empty($value),
                fn ($client) => $client->{$method}($value),
            );
        }

        $requestSteps = array_map(fn ($request): SendApiRequest => app(SendApiRequest::class, [
            'client' => clone $client,
            'request' => $request,
        ]), $apiSuite->requests);

        $memorized = [
            'memory' => [],
            'expectations' => [],
        ];

        return app(static::class)
            ->setSuite($apiSuite)
            ->through([...$requestSteps])
            // TODO: add pipe to notify/process after
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

        // TODO: notify
        throw $e;
    }
}
