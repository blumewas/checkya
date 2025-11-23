<?php declare(strict_types=1);

namespace App\Pipelines\ProcessSuite;

use Closure;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use RuntimeException;

readonly class SendApiRequest
{
    public function __construct(
        protected PendingRequest $client,
        protected array $request,
    ) {}

    public function handle(array $data, Closure $next): array
    {
        // Get config
        $url = $this->request['url'];
        $method = $this->request['method'] ?? 'get';

        // Get the headers
        $headers = array_reduce(
            $this->request['headers'] ?? [],
            function ($carry, $header) use ($data) {
                $name = $header['name'] ?? null;
                $value = $header['value'] ?? null;

                if (empty($name) || empty($value)) {
                    return $carry;
                }

                // Get the value from memory
                if (str_starts_with($value, 'memorized')) {
                    $jsonPath = explode('.', str_replace('memorized.', '', $value));
                    $value = $data['memory'];

                    foreach ($jsonPath as $part) {
                        $value = $value[$part] ?? null;

                        if (empty($value)) {
                            return $carry;
                        }
                    }
                }

                $carry[$name] = $value;

                return $carry;
            },
            [],
        );

        // Get response
        $response = $this->client
            ->withHeaders($headers)
            ->send($method, $url);

        // Process response data
        $responseData = $response->json();
        $responseActions = $this->request['response'] ?? [];

        foreach ($responseActions as $action) {
            $actionName = $action['action'] ?? null;

            if (empty($actionName)) {
                continue;
            }

            unset($action['action']);

            // Run the action
            $data = match ($actionName) {
                'memorize' => $this->memorize($data, $responseData, ...$action),
                'assert' => $this->assert($data, $response, ...$action),
                default => throw new RuntimeException('Unknown action to perform on response result', 1),
            };
        }

        return $next($data);
    }

    protected function assert(array $data, Response $response, array $expectations): array
    {
        $results = $data['expectations'] ?? [];

        foreach ($expectations as $expectation) {
            // Check whether we get a json key or a method
            $key = $expectation['key'] ?? null;
            $method = $expectation['method'] ?? null;

            if ($key === null && $method === null) {
                // skip
                continue;
            }

            $value = $expectation['value'] ?? null;
            $expectationKey = sprintf('%s = %s', $key ?? $method, $value);

            // Get expectation result
            $expectationResult = null;

            if (! empty($key)) {
                $expectationResult = $response->json($key) === $value;
            } elseif (! empty($method)) {
                $expectationResult = $value === $response->{$method}();
            }

            $results[$expectationKey] = $expectationResult;
        }

        // Set expectations
        $data['expectations'] = $results;

        return $data;
    }

    protected function memorize(array $data, array $response, string $key): array
    {
        // Save the key from response data to memory
        $memory = $data['memory'];
        $memory[$key] = $response[$key];

        $data['memory'] = $memory;

        return $data;
    }
}
