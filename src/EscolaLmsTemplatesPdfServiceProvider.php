<?php

namespace EscolaLms\TemplatesPdf;

use EscolaLms\TemplatesPdf\Providers\CourseTemplatesServiceProvider;
use EscolaLms\TemplatesPdf\Providers\UserTemplateServiceProvider;
use EscolaLms\TemplatesPdf\Providers\AuthServiceProvider;
use EscolaLms\TemplatesPdf\Services\ReportBroService;
use EscolaLms\TemplatesPdf\Services\Contracts\ReportBroServiceContract;
use Illuminate\Support\ServiceProvider;
use EscolaLms\Settings\Facades\AdministrableConfig;

/**
 * SWAGGER_VERSION
 */
class EscolaLmsTemplatesPdfServiceProvider extends ServiceProvider
{
    const CONFIG_KEY = 'escola_templates_pdf';

    public $singletons = [
        ReportBroServiceContract::class => ReportBroService::class
    ];

    public function register()
    {

        $this->mergeConfigFrom(__DIR__ . '/config.php', self::CONFIG_KEY);

        if (class_exists(\EscolaLms\Courses\EscolaLmsCourseServiceProvider::class)) {
            $this->app->register(CourseTemplatesServiceProvider::class);
        }

        $this->app->register(AuthServiceProvider::class);
        $this->app->register(UserTemplateServiceProvider::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'fabricjs');

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        if (class_exists(\EscolaLms\Settings\Facades\AdministrableConfig::class)) {
            AdministrableConfig::registerConfig(self::CONFIG_KEY . '.reportbro_url', ['required', 'string'], true);
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
