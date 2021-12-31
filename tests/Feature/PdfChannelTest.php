<?php

namespace EscolaLms\TemplatesPdf\Tests\Feature;

use EscolaLms\Core\Tests\ApiTestTrait;
use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Templates\Facades\Template;
use EscolaLms\Templates\Repository\Contracts\TemplateRepositoryContract;
use EscolaLms\TemplatesPdf\Core\EmailChannel;
use EscolaLms\TemplatesPdf\Core\EmailMailable;
use EscolaLms\TemplatesPdf\Database\Seeders\TemplatesPdfSeeder;
use EscolaLms\TemplatesPdf\Tests\Mocks\TestEvent;
use EscolaLms\TemplatesPdf\Tests\Mocks\TestVariables;
use EscolaLms\TemplatesPdf\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class PdfChannelTest extends TestCase
{
    use CreatesUsers, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        Template::register(TestEvent::class, EmailChannel::class, TestVariables::class);
        $this->seed(TemplatesPdfSeeder::class);
    }

    public function testPreview()
    {
        Mail::fake();
        Event::fake();
        Notification::fake();

        $admin = $this->makeAdmin();

        $template = app(TemplateRepositoryContract::class)->findTemplateDefault(TestEvent::class, EmailChannel::class);

        Template::sendPreview($admin, $template);

        Mail::assertSent(EmailMailable::class, function (EmailMailable $mailable) use ($admin) {
            $this->assertEquals(__('New friend request'), $mailable->subject);
            $this->assertTrue($mailable->hasTo($admin->email));
            return true;
        });
    }
}
