<?php declare(strict_types=1);

namespace App\Util;

use App\Data\TestResult;
use App\Models\ApiSuite;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;

class ApiSuiteClient extends PendingRequest
{
    public function __construct(
        protected ApiSuite $apiSuite,
        ?HttpFactory $factory = null,
        $middleware = [],
    ) {
        parent::__construct($factory, $middleware);

        foreach ($apiSuite->client_config as $method => $value) {
            $this->when(
                method_exists($this, $method) && ! empty($value),
                fn ($client) => $client->{$method}($value),
            );
        }
    }

    public static function make(ApiSuite $apiSuite): self
    {
        return new self(apiSuite: $apiSuite, factory: app(HttpFactory::class), middleware: []);
    }

    public function configure(array $config, TestResult $results): void
    {
        // Get the headers
        $headers = array_reduce(
            $config['headers'] ?? [],
            function ($carry, $header) use ($results) {
                $name = $header['name'] ?? null;
                $value = $header['value'] ?? null;

                if (empty($name) || empty($value)) {
                    return $carry;
                }

                // Get the secret value
                if (str_starts_with($value, 'secrets')) {
                    $value = $this->apiSuite->secretValue($value);
                }

                // Get the value from memory
                if (str_starts_with($value, 'memorized')) {
                    // get the memorized value
                    $value = $results->memorized($value);
                }

                $carry[$name] = $value;

                return $carry;
            },
            [],
        );

        $this->withHeaders($headers);
    }
}
