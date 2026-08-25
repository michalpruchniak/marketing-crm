<?php

namespace App\Providers;

use App\Repositories\ClientRepository;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\CredentialPayloadRepositoryInterface;
use App\Repositories\Contracts\CredentialRepositoryInterface;
use App\Repositories\CredentialPayloadRepository;
use App\Repositories\CredentialRepository;
use App\Services\ClientService;
use App\Services\Contracts\ClientServiceInterface;
use App\Services\Contracts\CredentialServiceInterface;
use App\Services\CredentialService;
use App\Supports\SecretsStorage\Clients\HashicorpVaultClient;
use App\Supports\SecretsStorage\Contracts\PasswordSecretStorageInterface;
use App\Supports\SecretsStorage\Factories\SecretsDriverStrategyFactory;
use App\Supports\SecretsStorage\PasswordSecretStorage;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SecretsDriverStrategyFactory::class);
        $this->app->singleton(PasswordSecretStorageInterface::class, PasswordSecretStorage::class);

        $this->app->singleton(HashicorpVaultClient::class, function (): HashicorpVaultClient {
            return new HashicorpVaultClient(
                address: (string) config('secrets.hashicorp.address'),
                token: (string) config('secrets.hashicorp.token'),
            );
        });

        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(CredentialRepositoryInterface::class, CredentialRepository::class);
        $this->app->bind(CredentialPayloadRepositoryInterface::class, CredentialPayloadRepository::class);

        $this->app->bind(CredentialServiceInterface::class, CredentialService::class);
        $this->app->bind(ClientServiceInterface::class, ClientService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
