<?php

namespace EscolaLms\TemplatesPdf\Tests\Feature;

use EscolaLms\Core\Tests\ApiTestTrait;
use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Templates\Facades\Template;
use EscolaLms\Templates\Repository\Contracts\TemplateRepositoryContract;
use EscolaLms\TemplatesPdf\Core\PdfChannel;
use EscolaLms\TemplatesPdf\Database\Seeders\TemplatesPdfSeeder;
use EscolaLms\TemplatesPdf\Tests\Mocks\TestEvent;
use EscolaLms\TemplatesPdf\Tests\Mocks\TestVariables;
use EscolaLms\TemplatesPdf\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

class PdfChannelTest extends TestCase
{
    use CreatesUsers, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        Template::register(TestEvent::class, PdfChannel::class, TestVariables::class);
        $this->seed(TemplatesPdfSeeder::class);
    }

    public function testPreview()
    {
        Event::fake();
        Notification::fake();

        $admin = $this->makeAdmin();

        $template = app(TemplateRepositoryContract::class)->findTemplateDefault(TestEvent::class, PdfChannel::class);

        $preview = Template::sendPreview($admin, $template);

        $this->assertTrue($preview->toArray()['sent']);
    }
}
