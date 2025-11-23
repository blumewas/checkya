<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;
use stdClass;
use Symfony\Component\Yaml\Yaml;
use Throwable;

class YamlConfigService
{
    /**
     * Validate a config against our schema.
     */
    public function validate(array|string $config): bool|ValidationError
    {
        if (is_string($config)) {
            $config = Yaml::parse($config, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE | Yaml::PARSE_CONSTANT);
        }

        $validator = new Validator;

        // Convert PHP array to stdClass structure (required by Opis)
        $data = json_decode(json_encode($config) ?: '{}');

        /** @var ValidationResult $result */
        $result = $validator->validate($data, $this->getJsonSchema());

        return $result->isValid() ? true : $result->error() ?? false;
    }

    /**
     * Parse the yaml config.
     */
    public function parse(string $yaml): array
    {
        try {
            $config = Yaml::parse($yaml, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE | Yaml::PARSE_CONSTANT);
        } catch (Throwable $th) {
            throw $th;
        }

        $this->validate($config);

        return $config;
    }

    private function getJsonSchema(): stdClass
    {
        $schema = File::get(resource_path('./schema/config.json'));

        return json_decode($schema);
    }
}
