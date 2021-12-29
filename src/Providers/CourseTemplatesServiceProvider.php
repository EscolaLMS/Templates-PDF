<?php

namespace EscolaLms\TemplatesPdf\Providers;

use EscolaLms\Courses\Events\EscolaLmsCourseAssignedTemplateEvent;
use EscolaLms\Courses\Events\EscolaLmsCourseDeadlineSoonTemplateEvent;
use EscolaLms\Courses\Events\EscolaLmsCourseFinishedTemplateEvent;
use EscolaLms\Courses\Events\EscolaLmsCourseUnassignedTemplateEvent;
use EscolaLms\Templates\Facades\Template;
use EscolaLms\TemplatesPdf\Core\PdfChannel;
use EscolaLms\TemplatesPdf\Courses\DeadlineIncomingVariables;
use EscolaLms\TemplatesPdf\Courses\UserAssignedToCourseVariables;
use EscolaLms\TemplatesPdf\Courses\UserFinishedCourseVariables;
use EscolaLms\TemplatesPdf\Courses\UserUnassignedFromCourseVariables;
use Illuminate\Support\ServiceProvider;

class CourseTemplatesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Template::register(EscolaLmsCourseFinishedTemplateEvent::class, PdfChannel::class, UserFinishedCourseVariables::class);
    }
}
