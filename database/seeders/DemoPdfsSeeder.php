<?php

namespace EscolaLms\TemplatesPdf\Database\Seeders;

use EscolaLms\Core\Enums\UserRole;
use EscolaLms\Courses\Database\Seeders\ProgressSeeder;
use EscolaLms\Courses\Enum\ProgressStatus;
use EscolaLms\Courses\Events\EscolaLmsCourseFinishedTemplateEvent;
use EscolaLms\Courses\Models\Topic;
use EscolaLms\Courses\Models\User;
use EscolaLms\Courses\Repositories\Contracts\CourseProgressRepositoryContract;
use EscolaLms\Courses\Repositories\CourseProgressRepository;
use EscolaLms\Courses\Services\Contracts\ProgressServiceContract;
use EscolaLms\Courses\Services\ProgressService;
use EscolaLms\Courses\ValueObjects\CourseProgressCollection;
use EscolaLms\Templates\Events\EventWrapper;
use EscolaLms\Templates\Facades\Template;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Event;

class DemoPdfsSeeder extends Seeder
{
    public function run()
    {
        if (!class_exists(\EscolaLms\Courses\EscolaLmsCourseServiceProvider::class)) {
            return;
        }

        Event::fake(EscolaLmsCourseFinishedTemplateEvent::class);

        /** @var ProgressService $progressService */
        $progressService = app(ProgressServiceContract::class);
        /** @var CourseProgressRepository $progressRepository */
        $progressRepository = app(CourseProgressRepositoryContract::class);

        $students = $this->getStudents();

        /** @var User&Authenticatable $student */
        foreach ($students as $student) {
            $progresses = $progressService->getByUser($student);

            if ($progresses->count() > 0) {
                /** @var CourseProgressCollection $courseProgress */
                $courseProgress = $progresses->first();
                $course = $courseProgress->getCourse();

                if (!$courseProgress->isFinished()) {
                    foreach ($course->topics as $topic) {
                        /** @var Topic $topic */
                        $progressRepository->updateInTopic($topic, $student, ProgressStatus::COMPLETE, rand(60, 300));
                    }
                    $progressService->update($course, $student, []);
                }

                Template::handleEvent(new EventWrapper(new EscolaLmsCourseFinishedTemplateEvent($student, $course)));
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
}
