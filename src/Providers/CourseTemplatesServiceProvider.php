<?php

namespace EscolaLms\TemplatesPdf\Providers;

use EscolaLms\Courses\Events\CourseFinished;
use EscolaLms\Templates\Facades\Template;
use EscolaLms\TemplatesPdf\Core\PdfChannel;
use EscolaLms\TemplatesPdf\Courses\UserFinishedCourseVariables;
use Illuminate\Support\ServiceProvider;

class CourseTemplatesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Template::register(CourseFinished::class, PdfChannel::class, UserFinishedCourseVariables::class);
    }
}
