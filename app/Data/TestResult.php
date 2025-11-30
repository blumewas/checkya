<?php declare(strict_types=1);

namespace App\Data;

use RuntimeException;
use Stringable;

class TestResult implements Stringable
{
    protected array $memory = [];
    protected array $expectationResults = [];

    public function memorize(string $key, mixed $value): void
    {
        $this->memory[$key] = $value;
    }

    /**
     * Get a memorized value.
     */
    public function memorized(string $key): mixed
    {
        $path = explode('.', str_replace('memorized.', '', $key));
        $value = $this->memory;

        foreach ($path as $part) {
            $value = $value[$part] ?? null;

            // TODO: custom exception
            throw_if(! $value, new RuntimeException('Value not found in memroy for $key'));
        }

        return $value;
    }

    /**
     * Add expectation result.
     */
    public function addExpectationResult(string $key, mixed $actual, bool $result): void
    {
        $this->expectationResults[$key] = [
            'actual' => $actual,
            'result' => $result,
        ];
    }

    public function expectationResult(): array
    {
        return $this->expectationResults;
    }

    public function toArray(): array
    {
        return [
            'memory' => $this->memory,
            'expectationResults' => $this->expectationResults,
        ];
    }

    public function __toString(): string
    {
        $value = json_encode($this->toArray());

        if (! $value) {
            return '{}';
        }

        return $value;
    }
}
