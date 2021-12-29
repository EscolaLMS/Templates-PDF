<?php

namespace EscolaLms\TemplatesPdf\Database\Seeders;

use EscolaLms\Templates\Facades\Template;
use EscolaLms\TemplatesPdf\Core\PdfChannel;
use Illuminate\Database\Seeder;

class TemplatesPdfSeeder extends Seeder
{
    public function run()
    {
        Template::createDefaultTemplatesForChannel(PdfChannel::class);
    }
}
