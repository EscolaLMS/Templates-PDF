<?php

namespace EscolaLms\TemplatesPdf\Tests;

use EscolaLms\Categories\EscolaLmsCategoriesServiceProvider;
use EscolaLms\Core\Models\User;
use EscolaLms\TemplatesPdf\EscolaLmsTemplatesPdfServiceProvider;
use EscolaLms\Templates\Database\Seeders\PermissionTableSeeder as TemplatesPermissionTableSeeder;
use EscolaLms\TemplatesPdf\Database\Seeders\PermissionTableSeeder as TemplatesPdfPermissionTableSeeder;

use EscolaLms\Templates\EscolaLmsTemplatesServiceProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\PassportServiceProvider;
use Spatie\Permission\PermissionServiceProvider;
use EscolaLms\Courses\EscolaLmsCourseServiceProvider;

class TestCase extends \EscolaLms\Core\Tests\TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TemplatesPermissionTableSeeder::class);
        $this->seed(TemplatesPdfPermissionTableSeeder::class);
    }

    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            PassportServiceProvider::class,
            PermissionServiceProvider::class,
            EscolaLmsCourseServiceProvider::class,
            EscolaLmsTemplatesServiceProvider::class,
            EscolaLmsCategoriesServiceProvider::class,
            EscolaLmsTemplatesPdfServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('passport.client_uuids', true);
    }
}
