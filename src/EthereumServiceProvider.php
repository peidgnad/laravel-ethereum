<?php

namespace Peidgnad\LaravelEthereum;

use Illuminate\Support\ServiceProvider;
use Peidgnad\LaravelEthereum\Console\EventListenCommand;

class EthereumServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/ethereum.php', 'ethereum'
        );
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerCommands();
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    private function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ethereum.php' => config_path('ethereum.php'),
            ], 'ethereum-config');
        }
    }

    /**
     * Register the package's commands.
     *
     * @return void
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                EventListenCommand::class,
            ]);
        }
    }
}
