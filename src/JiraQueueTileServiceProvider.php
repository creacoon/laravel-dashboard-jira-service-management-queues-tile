<?php

namespace Creacoon\JiraQueueServiceTile;

use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class JiraQueueTileServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('queue')
            ->hasViews()
            ->hasCommand(FetchDataFromJiraQueueCommand::class);
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FetchDataFromJiraQueueCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/creacoon/dashboard-jira-queue-tile'),
        ], 'dashboard-jira-queue-tile-views');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dashboard-jira-queue-tile');

        Livewire::component('jira-service-queue-tile', JiraQueueTileServiceManagementComponent::class);
    }
}
