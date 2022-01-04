<?php

namespace EscolaLms\TemplatesPdf\Providers;

use EscolaLms\Courses\Events\EscolaLmsCourseFinishedTemplateEvent;
use EscolaLms\Templates\Facades\Template;
use EscolaLms\TemplatesPdf\Core\PdfChannel;
use EscolaLms\TemplatesPdf\Courses\UserFinishedCourseVariables;
use Illuminate\Support\ServiceProvider;

class CourseTemplatesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Template::register(EscolaLmsCourseFinishedTemplateEvent::class, PdfChannel::class, UserFinishedCourseVariables::class);
    }
}
