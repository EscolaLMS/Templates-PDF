<?php

namespace EscolaLms\TemplatesPdf\Database\Seeders;

use EscolaLms\Core\Enums\UserRole;
use EscolaLms\Courses\Database\Seeders\ProgressSeeder;
use EscolaLms\Courses\Enum\ProgressStatus;
use EscolaLms\Courses\Events\EscolaLmsCourseFinishedTemplateEvent;
use EscolaLms\Courses\Models\User;
use EscolaLms\Courses\Repositories\Contracts\CourseProgressRepositoryContract;
use EscolaLms\Courses\Services\Contracts\ProgressServiceContract;
use EscolaLms\Courses\ValueObjects\CourseProgressCollection;
use EscolaLms\Templates\Events\EventWrapper;
use EscolaLms\Templates\Facades\Template;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Event;

class DemoPdfsSeeder extends Seeder
{
    protected ProgressServiceContract $progressService;
    protected CourseProgressRepositoryContract $progressRepository;

    public function __construct()
    {
        $this->progressService = app(ProgressServiceContract::class);
        $this->progressRepository = app(CourseProgressRepositoryContract::class);
    }

    public function run()
    {
        if (!class_exists(\EscolaLms\Courses\EscolaLmsCourseServiceProvider::class)) {
            return;
        }

        Event::fake(EscolaLmsCourseFinishedTemplateEvent::class);

        $students = $this->getStudents();

        /** @var User&Authenticatable $student */
        foreach ($students as $student) {
            $progresses = $this->progressService->getByUser($student);

            if ($progresses->count() > 0) {
                /** @var CourseProgressCollection $courseProgress */
                $courseProgress = $progresses->first();

                $this->ensureCourseIsFinished($courseProgress);

                Template::handleEvent(new EventWrapper(new EscolaLmsCourseFinishedTemplateEvent($student, $courseProgress->getCourse())));
            }
        }
    }

    protected function getStudents(): Collection
    {
        $students = User::role(UserRole::STUDENT)->whereHas('courses')->inRandomOrder()->take(5)->get();
        if ($students->isEmpty() && class_exists(\EscolaLms\TopicTypes\EscolaLmsTopicTypesServiceProvider::class)) {
            $this->call(ProgressSeeder::class);
            $students = User::role(UserRole::STUDENT)->whereHas('courses')->inRandomOrder()->take(5)->get();
        }
        return $students;
    }

    protected function ensureCourseIsFinished(CourseProgressCollection $courseProgress): void
    {
        if (!$courseProgress->isFinished()) {
            $course = $courseProgress->getCourse();
            foreach ($course->topics as $topic) {
                $this->progressRepository->updateInTopic($topic, $courseProgress->getUser(), ProgressStatus::COMPLETE, rand(60, 300));
            }
            $this->progressService->update($course, $courseProgress->getUser(), []);
        }
    }
}
