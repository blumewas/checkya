<?php declare(strict_types=1);

namespace App\Providers;

use Bepsvpt\SecureHeaders\SecureHeadersMiddleware;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider as LaravelTelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(LaravelTelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    public function boot(): void
    {
        $this->configureModels();
        $this->configureCarbon();
        $this->configureVite();
        $this->configureProhibitDestructiveCommands();

        if (! $this->app->isLocal()) {
            $kernel = $this->app->make(Kernel::class);
            $kernel->pushMiddleware(SecureHeadersMiddleware::class);
        }
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict(! app()->isProduction());
        Model::automaticallyEagerLoadRelationships();
    }

    private function configureCarbon(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }

    private function configureProhibitDestructiveCommands(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());
    }
}
