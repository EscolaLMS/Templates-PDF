<?php

namespace EscolaLms\TemplatesPdf;

use EscolaLms\TemplatesPdf\Models\FabricPDF;
use EscolaLms\TemplatesPdf\Policies\TemplatePdfPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        FabricPDF::class => TemplatePdfPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
