<?php declare(strict_types=1);

use App\Services\YamlConfigService;

it('can validate configs', function (string $config): void {
    $result = (new YamlConfigService)->validate($config);

    expect($result)->toBeTrue();
})->with([
    <<<'YAML'
        client:
          baseUrl: 'http://localhost:3000'
          accept: 'application/json'
          withHeaders:
            foo: 'bar'
            bar: 'baz'
        requests:
          - url: '/api/authorize'
            headers:
              - name: 'x-context-password'
                value: 'password'
              - name: 'Authorization'
                value: 'memorized.data.token'
            response:
              - action: 'memorize'
                key: 'data'
              - action: 'assert'
                expectations:
                  - method: 'status'
                    value: 200
                  - key: 'name'
                    value: 'John Doe'
        YAML,
]);
