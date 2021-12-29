<?php

namespace EscolaLms\TemplatesPdf\Tests\Api;

use EscolaLms\Core\Models\User as CoreUser;
use EscolaLms\Core\Tests\ApiTestTrait;
use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Courses\Events\EscolaLmsCourseAssignedTemplateEvent;
use EscolaLms\Courses\Events\EscolaLmsCourseDeadlineSoonTemplateEvent;
use EscolaLms\Courses\Events\EscolaLmsCourseFinishedTemplateEvent;
use EscolaLms\Courses\Events\EscolaLmsCourseUnassignedTemplateEvent;
use EscolaLms\Courses\Jobs\CheckForDeadlines;
use EscolaLms\Courses\Models\Course;
use EscolaLms\Courses\Models\Lesson;
use EscolaLms\Courses\Models\Topic;
use EscolaLms\Courses\Models\User;
use EscolaLms\Courses\Tests\ProgressConfigurable;
use EscolaLms\Courses\ValueObjects\CourseProgressCollection;
use EscolaLms\Templates\Listeners\TemplateEventListener;
use EscolaLms\TemplatesPdf\Core\EmailMailable;
use EscolaLms\TemplatesPdf\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

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
    }

    public function testDeadlineNotification()
    {
        Notification::fake();
        Event::fake();
        Mail::fake();

        $user = User::factory()->create();
        $course = Course::factory()->create(['active' => true, 'active_to' => Carbon::now()->addDays(config('escolalms_courses.reminder_of_deadline_count_days'))]);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->getKey()
        ]);
        $topics = Topic::factory(2)->create([
            'lesson_id' => $lesson->getKey(),
            'active' => true,
        ]);
        $user->courses()->save($course);
        $progress = CourseProgressCollection::make($user, $course);

        $checkForDealines = new CheckForDeadlines();
        $checkForDealines->handle();

        Event::assertDispatched(EscolaLmsCourseDeadlineSoonTemplateEvent::class, function (EscolaLmsCourseDeadlineSoonTemplateEvent $event) use ($user, $course) {
            return $event->getCourse()->getKey() === $course->getKey() && $event->getUser()->getKey() === $user->getKey();
        });

        $listener = app(TemplateEventListener::class);
        $listener->handle(new EscolaLmsCourseDeadlineSoonTemplateEvent($user, $course));

        Mail::assertSent(EmailMailable::class, function (EmailMailable $mailable) use ($user, $course) {
            $this->assertEquals(__('Deadline for course ":course"', ['course' => $course->title]), $mailable->subject);
            $this->assertTrue($mailable->hasTo($user->email));
            return true;
        });
    }

    public function testUserAssignedToCourseNotification()
    {
        Notification::fake();
        Event::fake();
        Mail::fake();

        $admin = $this->makeAdmin();

        $course = Course::factory()->create([
            'author_id' => $admin->id,
            'base_price' => 1337,
            'active' => true
        ]);

        $student = User::factory()->create();

        $this->response = $this->actingAs($admin, 'api')->post('/api/admin/courses/' . $course->id . '/access/add/', [
            'users' => [$student->getKey()]
        ]);

        $this->response->assertOk();

        $user = CoreUser::find($student->getKey());
        Event::assertDispatched(EscolaLmsCourseAssignedTemplateEvent::class, function (EscolaLmsCourseAssignedTemplateEvent $event) use ($user, $course) {
            return $event->getCourse()->getKey() === $course->getKey() && $event->getUser()->getKey() === $user->getKey();
        });

        $listener = app(TemplateEventListener::class);
        $listener->handle(new EscolaLmsCourseAssignedTemplateEvent($user, $course));

        Mail::assertSent(EmailMailable::class, function (EmailMailable $mailable) use ($user, $course) {
            $this->assertEquals(__('You have been assigned to ":course"', ['course' => $course->title]), $mailable->subject);
            $this->assertTrue($mailable->hasTo($user->email));
            return true;
        });
    }

    public function testUserUnassignedFromCourseNotification()
    {
        Notification::fake();
        Event::fake();
        Mail::fake();

        $admin = $this->makeAdmin();

        $course = Course::factory()->create([
            'author_id' => $admin->id,
            'base_price' => 1337,
            'active' => true
        ]);
        $student = User::factory()->create();
        $student->courses()->save($course);

        $this->response = $this->actingAs($admin, 'api')->post('/api/admin/courses/' . $course->id . '/access/remove/', [
            'users' => [$student->getKey()]
        ]);

        $this->response->assertOk();

        $user = CoreUser::find($student->getKey());
        Event::assertDispatched(EscolaLmsCourseUnassignedTemplateEvent::class, function (EscolaLmsCourseUnassignedTemplateEvent $event) use ($user, $course) {
            return $event->getCourse()->getKey() === $course->getKey() && $event->getUser()->getKey() === $user->getKey();
        });

        $listener = app(TemplateEventListener::class);
        $listener->handle(new EscolaLmsCourseUnassignedTemplateEvent($user, $course));

        Mail::assertSent(EmailMailable::class, function (EmailMailable $mailable) use ($user, $course) {
            $this->assertEquals(__('You have been unassigned from ":course"', ['course' => $course->title]), $mailable->subject);
            $this->assertTrue($mailable->hasTo($user->email));
            return true;
        });
    }

    public function testUserFinishedCourseNotification()
    {
        Notification::fake();
        Event::fake();
        Mail::fake();

        $course = Course::factory()->create(['active' => true]);
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

        // TODO: this event is not dispatched anywhere in Courses package, uncomment when it's fixed
        //Event::assertDispatched(EscolaLmsCourseFinishedTemplateEvent::class);
        //Event::assertDispatched(EscolaLmsCourseFinishedTemplateEvent::class, function (EscolaLmsCourseFinishedTemplateEvent $event) use ($user, $course) {
        //    return $event->getCourse()->getKey() === $course->getKey() && $event->getUser()->getKey() === $user->getKey();
        //});

        $listener = app(TemplateEventListener::class);
        $listener->handle(new EscolaLmsCourseFinishedTemplateEvent($user, $course));

        Mail::assertSent(EmailMailable::class, function (EmailMailable $mailable) use ($user, $course) {
            $this->assertEquals(__('You finished ":course"', ['course' => $course->title]), $mailable->subject);
            $this->assertTrue($mailable->hasTo($user->email));
            return true;
        });

        if (!Event::hasDispatched(EscolaLmsCourseFinishedTemplateEvent::class)) {
            $this->markTestIncomplete(
                'EscolaLmsCourseFinishedTemplateEvent is not dispatched in Courses'
            );
        }
    }
}
