<?php

namespace Creacoon\JiraQueueServiceTile;

use Creacoon\JiraQueueServiceTile\JiraQueueTileComponent;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Creacoon\JiraQueueServiceTile\FetchDataFromJiraQueueCommand;

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
            ->name('skeleton')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_skeleton_table')
            ->hasCommand(FetchDataFromJiraQueueCommand::class);
    }
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Creacoon\JiraQueueServiceTile\FetchDataFromJiraQueueCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/creacoon/dashboard-queue-tile'),
        ], 'dashboard-queue-tile-views');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dashboard-queue-tile');

        Livewire::component('queue-tile', JiraQueueTileComponent::class);
    }
}
