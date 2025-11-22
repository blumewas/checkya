<?php declare(strict_types=1);

namespace App\Providers;

use Chiiya\FilamentAccessControl\FilamentAccessControlPlugin;
use Chiiya\FilamentAccessControl\Http\Middleware\EnsureAccountIsNotExpired;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->plugin(FilamentAccessControlPlugin::make())
            ->colors([
                'primary' => [
                    50 => '#FEF2F4',
                    100 => '#FDE6EA',
                    200 => '#FACFD5',
                    300 => '#F5A3B1',
                    400 => '#DE0639',
                    500 => '#DE0639',
                    600 => '#DE0639',
                    700 => '#A6042B',
                    800 => '#8A0328',
                    900 => '#750327',
                    950 => '#410112',
                ],
            ])
            ->resources([])
            ->pages([Dashboard::class])
            ->widgets([AccountWidget::class])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class, EnsureAccountIsNotExpired::class]);
    }
}
