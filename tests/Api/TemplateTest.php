<?php

namespace EscolaLms\TemplatesPdf\Tests\Api;

use EscolaLms\Core\Tests\ApiTestTrait;
use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Templates\Database\Seeders\PermissionTableSeeder;
use EscolaLms\Templates\Events\ManuallyTriggeredEvent;
use EscolaLms\Templates\Listeners\TemplateEventListener;
use EscolaLms\Templates\Models\Template;
use EscolaLms\Templates\Models\TemplateSection;
use EscolaLms\TemplatesPdf\Core\PdfChannel;
use EscolaLms\TemplatesPdf\Core\UserVariables;
use EscolaLms\TemplatesPdf\Events\PdfCreated;
use EscolaLms\TemplatesPdf\Models\FabricPDF;
use EscolaLms\TemplatesPdf\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Event;

class TemplateTest extends TestCase
{
    use CreatesUsers, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionTableSeeder::class);
    }

    public function testManuallyTriggeredEvent(): void
    {
        Event::fake([
            ManuallyTriggeredEvent::class,
            PdfCreated::class,
        ]);

        $template = Template::factory()->create([
            'name' => 'Pdf',
            'channel' => PdfChannel::class,
            'event' => ManuallyTriggeredEvent::class,
            'default' => true,
        ]);

        $titleSection = TemplateSection::factory()->create([
            'key' => 'title',
            'content' => 'Pdf for @VarUserName',
            'template_id' => $template->getKey()
        ]);

        $contentSection = TemplateSection::factory()->create([
            'key' => 'content',
            'content' => '{"version":"4.6.0","objects":[]}',
            'template_id' => $template->getKey()
        ]);

        $student = $this->makeStudent();
        $admin = $this->makeAdmin();

        $this->response = $this->actingAs($admin, 'api')->postJson(
            '/api/admin/events/trigger-manually',
            ['users' => [$student->getKey()]]
        )->assertOk();

        Event::assertDispatched(ManuallyTriggeredEvent::class, function (ManuallyTriggeredEvent $event) use ($student){
            $this->assertEquals($student->getKey(), $event->getUser()->getKey());
            return true;
        });

        $listener = app(TemplateEventListener::class);
        $listener->handle(new ManuallyTriggeredEvent($student));

        Event::assertDispatched(PdfCreated::class);

        $pdf = FabricPDF::where('user_id', $student->getKey())->latest()->first();

        $this->assertEquals(str_replace(UserVariables::VAR_USER_NAME, $student->name, $titleSection->content), $pdf->title);
    }
}
