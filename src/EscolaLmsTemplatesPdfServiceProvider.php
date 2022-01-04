<?php

namespace EscolaLms\TemplatesPdf;

use EscolaLms\TemplatesPdf\Providers\CourseTemplatesServiceProvider;
use Illuminate\Support\ServiceProvider;

/**
 * SWAGGER_VERSION
 */
class EscolaLmsTemplatesPdfServiceProvider extends ServiceProvider
{
    const CONFIG_KEY = 'escola_templates_pdf';

    public $singletons = [];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', self::CONFIG_KEY);

        if (class_exists(\EscolaLms\Courses\EscolaLmsCourseServiceProvider::class)) {
            $this->app->register(CourseTemplatesServiceProvider::class);
        }
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function bootForConsole()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/config.php' => config_path(self::CONFIG_KEY . '.php'),
        ], self::CONFIG_KEY . '.config');
    }
}
