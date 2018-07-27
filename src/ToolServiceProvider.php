<?php

namespace GFL\Tool;
use Illuminate\Support\ServiceProvider;

class ToolServiceProvider extends ServiceProvider
{

    protected $commands = [
        'GFL\Tool\commands\CreateTable53B',
        'GFL\Tool\commands\CreateTable54B',
        'GFL\Tool\commands\CreateTable56B',
    ];

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }

        $this->publishes([
            __DIR__ . '/database' => public_path('/exels')
        ], 'seed');

        $this->publishes([
            __DIR__.'/config/tool.php' => config_path('tool.php'),
        ], 'config');
    }

    public function register(){
        $this->app->bind('Tool', function(){
            return $this->app->make('GFL\Tool\Tool');
        });
    }

}
