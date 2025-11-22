<?php declare(strict_types=1);

use Chiiya\CodeStyle\CodeStyle;
use Rector\Config\RectorConfig;

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

return static function (RectorConfig $config): void {
    $config->parallel();
    $config->paths([
        app_path(),
        config_path(),
        base_path('tests'),
    ]);
    $config->skip([
        __DIR__.'/app/*/node_modules/*',
    ]);
    $config->importNames();
    $config->import(CodeStyle::RECTOR);
};
