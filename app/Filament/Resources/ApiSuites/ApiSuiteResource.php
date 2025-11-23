<?php declare(strict_types=1);

namespace App\Filament\Resources\ApiSuites;

use App\Filament\Resources\ApiSuites\Pages\CreateApiSuite;
use App\Filament\Resources\ApiSuites\Pages\EditApiSuite;
use App\Filament\Resources\ApiSuites\Pages\ListApiSuites;
use App\Filament\Resources\ApiSuites\Pages\ViewApiSuite;
use App\Filament\Resources\ApiSuites\Schemas\ApiSuiteForm;
use App\Filament\Resources\ApiSuites\Schemas\ApiSuiteInfolist;
use App\Filament\Resources\ApiSuites\Tables\ApiSuitesTable;
use App\Models\ApiSuite;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ApiSuiteResource extends Resource
{
    protected static ?string $model = ApiSuite::class;
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ApiSuiteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ApiSuiteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApiSuitesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiSuites::route('/'),
            'create' => CreateApiSuite::route('/create'),
            'view' => ViewApiSuite::route('/{record}'),
            'edit' => EditApiSuite::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament/api_suites.model_label');
    }
}
