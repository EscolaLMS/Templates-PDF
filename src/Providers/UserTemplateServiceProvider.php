<?php

namespace EscolaLms\TemplatesPdf\Providers;

use EscolaLms\Templates\Events\ManuallyTriggeredEvent;
use EscolaLms\Templates\Facades\Template;
use EscolaLms\TemplatesPdf\Core\PdfChannel;
use EscolaLms\TemplatesPdf\Core\UserVariables;
use Illuminate\Support\ServiceProvider;

class UserTemplateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Template::register(ManuallyTriggeredEvent::class, PdfChannel::class, UserVariables::class);
    }
}
