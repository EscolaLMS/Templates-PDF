<?php

namespace EscolaLms\TemplatesPdf\Tests\Api;

use Carbon\Carbon;
use EscolaLms\Categories\Models\Category;
use EscolaLms\Core\Models\User as CoreUser;
use EscolaLms\Core\Tests\ApiTestTrait;
use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Courses\Enum\CourseStatusEnum;
use EscolaLms\Courses\Events\CourseFinished;
use EscolaLms\Courses\Models\Course;
use EscolaLms\Courses\Models\Lesson;
use EscolaLms\Courses\Models\Topic;
use EscolaLms\Courses\Models\User;
use EscolaLms\Courses\Tests\ProgressConfigurable;
use EscolaLms\Courses\ValueObjects\CourseProgressCollection;
use EscolaLms\Templates\Listeners\TemplateEventListener;
use EscolaLms\Templates\Models\Template;
use EscolaLms\TemplatesPdf\Core\PdfChannel;
use EscolaLms\TemplatesPdf\Courses\UserFinishedCourseVariables;
use EscolaLms\TemplatesPdf\Database\Seeders\TemplatesPdfSeeder;
use EscolaLms\TemplatesPdf\Events\PdfCreated;
use EscolaLms\TemplatesPdf\Models\FabricPDF;
use EscolaLms\TemplatesPdf\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class CoursesTest extends TestCase
{
    use CreatesUsers, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;
    use ProgressConfigurable;

    public function setUp(): void
    {
        parent::setUp();
        if (!class_exists(\EscolaLms\Courses\EscolaLmsCourseServiceProvider::class)) {
            $this->markTestSkipped('Courses package not installed');
        }
        if (!class_exists(\EscolaLms\Scorm\EscolaLmsScormServiceProvider::class)) {
            $this->markTestSkipped('Scorm package not installed');
        }
        $this->seed(TemplatesPdfSeeder::class);
    }

    public function testUserFinishedCourseNotification(): void
    {
        Notification::fake();
        Event::fake([
            CourseFinished::class,
            PdfCreated::class,
        ]);

        $course = Course::factory()->create(['status' => CourseStatusEnum::PUBLISHED, 'active_from' => Carbon::now()]);
        $course->categories()->attach(Category::factory()->create());
        $course->categories()->attach(Category::factory()->create());

        $lesson = Lesson::factory([
            'course_id' => $course->getKey()
        ])->create();
        $topics = Topic::factory(2)->create([
            'lesson_id' => $lesson->getKey(),
            'active' => true,
        ]);

        $student = User::factory([
            'points' => 0,
        ])->create();

        $courseProgress = CourseProgressCollection::make($student, $course);
        $this->assertFalse($courseProgress->isFinished());

        $this->response = $this->actingAs($student, 'api')->json(
            'PATCH',
            '/api/courses/progress/' . $course->getKey(),
            ['progress' => $this->getProgressUpdate($course)]
        );
        $courseProgress = CourseProgressCollection::make($student, $course);
        $this->response->assertOk();
        $this->assertTrue($courseProgress->isFinished());

        $user = CoreUser::find($student->getKey());

        Event::assertDispatched(CourseFinished::class);
        Event::assertDispatched(CourseFinished::class, function (CourseFinished $event) use ($user, $course) {
            return $event->getCourse()->getKey() === $course->getKey() && $event->getUser()->getKey() === $user->getKey();
        });

        Event::assertNotDispatched(PdfCreated::class);

        Log::listen(
            fn (MessageLogged $message) =>
            $this->assertNotEquals('error', $message->level, $message->message)
        );
        $listener = app(TemplateEventListener::class);
        $listener->handle(new CourseFinished($user, $course));

        Event::assertDispatched(PdfCreated::class);

        $template = Template::where('event', CourseFinished::class)->where('channel', PdfChannel::class)->where('default', true)->first();
        $pdf = FabricPDF::where('user_id', $user->getKey())->latest()->first();

        $section = $template->sections->where('key', 'title')->first();

        $this->assertEquals(str_replace(UserFinishedCourseVariables::VAR_COURSE_TITLE, $course->title, $section->content), $pdf->title);
    }

    public function testUserFinishedCoursePDFfromReportBro(): void
    {

        Http::fake([
            "https://reportbro.stage.etd24.pl/reportbro/report/run" => Http::response('key:123'),
            "https://reportbro.stage.etd24.pl/reportbro/report/run?*" => Http::response([UploadedFile::fake()->create('TestWhatsapp.pdf', 10)->get()])
        ]);


        Notification::fake();
        Event::fake([
            CourseFinished::class,
            PdfCreated::class,
        ]);

        $course = Course::factory()->create(['status' => CourseStatusEnum::PUBLISHED]);
        $lesson = Lesson::factory([
            'course_id' => $course->getKey()
        ])->create();
        $topics = Topic::factory(2)->create([
            'lesson_id' => $lesson->getKey(),
            'active' => true,
        ]);

        $student = User::factory([
            'points' => 0,
        ])->create();

        $courseProgress = CourseProgressCollection::make($student, $course);
        $this->assertFalse($courseProgress->isFinished());

        $this->response = $this->actingAs($student, 'api')->json(
            'PATCH',
            '/api/courses/progress/' . $course->getKey(),
            ['progress' => $this->getProgressUpdate($course)]
        );
        $courseProgress = CourseProgressCollection::make($student, $course);
        $this->response->assertOk();
        $this->assertTrue($courseProgress->isFinished());

        $user = CoreUser::find($student->getKey());

        Event::assertDispatched(CourseFinished::class);
        Event::assertDispatched(CourseFinished::class, function (CourseFinished $event) use ($user, $course) {
            return $event->getCourse()->getKey() === $course->getKey() && $event->getUser()->getKey() === $user->getKey();
        });

        Event::assertNotDispatched(PdfCreated::class);

        Log::listen(
            fn (MessageLogged $message) =>
            $this->assertNotEquals('error', $message->level, $message->message)
        );
        $listener = app(TemplateEventListener::class);
        $listener->handle(new CourseFinished($user, $course));

        Event::assertDispatched(PdfCreated::class);

        $template = Template::where('event', CourseFinished::class)->where('channel', PdfChannel::class)->where('default', true)->first();
        $pdf = FabricPDF::where('user_id', $user->getKey())->latest()->first();

        $response = $this->actingAs($student, 'api')->get(
            '/api/pdfs/generate/' . $pdf->getKey(),
        );

        $response->assertDownload();
    }
}
