<?php

namespace EscolaLms\TemplatesPdf\Tests\Api\Admin;

use EscolaLms\Core\Tests\ApiTestTrait;
use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Settings\Database\Seeders\PermissionTableSeeder;
use EscolaLms\Settings\Facades\AdministrableConfig;
use EscolaLms\TemplatesPdf\EscolaLmsTemplatesPdfServiceProvider;
use EscolaLms\TemplatesPdf\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Config;

class ConfigTest extends TestCase
{
    use CreatesUsers, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        if (!class_exists(\EscolaLms\Settings\EscolaLmsSettingsServiceProvider::class)) {
            $this->markTestSkipped('Settings package not installed');
        }
        Config::set('escola_settings.use_database', true);
        $this->seed(PermissionTableSeeder::class);
    }

    public function testEditDefaultMjmlTemplate()
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'api')->json(
            'GET',
            '/api/admin/config'
        );
        $response->assertOk();

        $json = $response->json();
        $this->assertNotNull($json['data'][EscolaLmsTemplatesPdfServiceProvider::CONFIG_KEY]['mjml']['default_template']['value']);
        $this->assertStringNotContainsString('MODIFIED TEMPLATE', $json['data'][EscolaLmsTemplatesPdfServiceProvider::CONFIG_KEY]['mjml']['default_template']['value']);

        $response = $this->actingAs($admin, 'api')->json(
            'POST',
            '/api/admin/config',
            [
                'config' => [
                    [
                        'key' => EscolaLmsTemplatesPdfServiceProvider::CONFIG_KEY . '.mjml.default_template',
                        'value' => <<<MJML_TEMPLATE
                                <mjml>
                                <mj-body>
                                    <mj-section background-color="#f0f0f0">
                                        <mj-column>
                                            <mj-text font-size="20px" color="#626262">
                                                @VarAppName
                                            </mj-text>
                                        </mj-column>
                                    </mj-section>
                                    <mj-section background-color="white">
                                        <mj-column>
                                            MODIFIED TEMPLATE
                                            @VarTemplateContent
                                        </mj-column>
                                    </mj-section>
                                </mj-body>
                                </mjml>
                                MJML_TEMPLATE,
                    ],
                ]
            ]
        );
        $response->assertOk();

        $response = $this->actingAs($admin, 'api')->json(
            'GET',
            '/api/admin/config'
        );
        $response->assertOk();

        $json = $response->json();
        $this->assertNotNull($json['data'][EscolaLmsTemplatesPdfServiceProvider::CONFIG_KEY]['mjml']['default_template']['value']);
        $this->assertStringContainsString('MODIFIED TEMPLATE', $json['data'][EscolaLmsTemplatesPdfServiceProvider::CONFIG_KEY]['mjml']['default_template']['value']);
    }
}
