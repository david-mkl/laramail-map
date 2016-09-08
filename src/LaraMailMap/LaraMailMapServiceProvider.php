<?php

namespace LaraMailMap;

use Illuminate\Support\ServiceProvider;
use LaraMailMap\MailMapModel;
use MailMap\Contracts\MailFactory as MailFactoryContract;
use MailMap\MailFactory;
use MailMap\MailMap;

class LaraMailMapServiceProvider extends ServiceProvider
{
    /**
     * Register MailMap factories
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MailFactoryContract::class, function () {
            return new MailFactory;
        });
        $this->app->singleton(MailMap::class, function ($app) {
            return new MailMap(config('mailmap'), $app->make(MailFactoryContract::class));
        });

        $this->mergeConfigFrom(
            __DIR__.'/config/mailmap.php', 'mailmap'
        );
    }

    /**
     * Publish the plugin configuration and boot the MailMapModel
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/mailmap.php' => config_path('mailmap.php')
        ]);

        MailMapModel::setImapConnection($this->app->make(MailMap::class));
    }
}
