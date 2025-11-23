<?php declare(strict_types=1);

namespace App\Pipelines;

use App\Models\ApiSuite;
use App\Pipelines\ProcessSuite\SendApiRequest;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Pipeline\Pipeline;
use Throwable;

class ProcessSuitePipeline extends Pipeline
{
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
            ->through([...$requestSteps])
            // TODO: add pipe to notify/process after
            ->send($memorized)
            ->thenReturn();
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
        // TODO: notify
        throw $e;
    }
}
