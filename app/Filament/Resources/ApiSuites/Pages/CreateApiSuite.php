<?php declare(strict_types=1);

namespace App\Filament\Resources\ApiSuites\Pages;

use App\Filament\Resources\ApiSuites\ApiSuiteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApiSuite extends CreateRecord
{
    protected static string $resource = ApiSuiteResource::class;
}
