<?php declare(strict_types=1);

namespace App\Pipelines\ProcessSuite;

use App\Data\TestResult;
use App\Models\ApiSuite;
use App\Util\ApiSuiteClient;
use Closure;
use Illuminate\Http\Client\Response;
use RuntimeException;

readonly class SendApiRequest
{
    public function __construct(
        protected ApiSuiteClient $client,
        protected ApiSuite $apiSuite,
        protected array $request,
    ) {}

    public function handle(TestResult $results, Closure $next): TestResult
    {
        $this->client->configure($this->request, $results);

        // Get config
        $url = $this->request['url'];
        $method = $this->request['method'] ?? 'get';

        // Get response
        $response = $this->client
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
            $results = match ($actionName) {
                'memorize' => $this->memorize($results, $responseData, ...$action),
                'assert' => $this->assert($results, $response, ...$action),
                default => throw new RuntimeException('Unknown action to perform on response result', 1),
            };
        }

        return $next($results);
    }

    protected function assert(TestResult $results, Response $response, array $expectations): TestResult
    {
        foreach ($expectations as $expectation) {
            // Check whether we get a json key or a method
            $key = $expectation['key'] ?? null;
            $method = $expectation['method'] ?? null;

            if ($key === null && $method === null) {
                // skip
                continue;
            }

            $expectedValue = $expectation['value'] ?? null;
            $expectationKey = sprintf('%s = %s', $key ?? $method, $expectedValue);

            // Get the value
            $value = null;

            if (! empty($key)) {
                $value = $response->json($key);
            } elseif (! empty($method)) {
                $value = $response->{$method}();
            }

            // Get expectation result
            $expectationResult = $value === $expectedValue;

            $results->addExpectationResult($expectationKey, $value, $expectationResult);
        }

        return $results;
    }

    /**
     * Memorize a value from response data.
     */
    protected function memorize(TestResult $results, array $response, string $key): TestResult
    {
        // Save the key from response data to memory
        $results->memorize($key, $response[$key]);

        return $results;
    }
}
