<?php declare(strict_types=1);

namespace App\Filament\Resources\ApiSuites\Pages;

use App\Filament\Resources\ApiSuites\ApiSuiteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApiSuites extends ListRecords
{
    protected static string $resource = ApiSuiteResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
