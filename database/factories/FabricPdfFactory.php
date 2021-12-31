<?php

namespace EscolaLms\TemplatesPdf\Database\Factories;

use EscolaLms\TemplatesPdf\Models\FabricPDF;
use EscolaLms\Templates\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;
use EscolaLms\Auth\Models\User;

class FabricPDFFactory extends Factory
{
    protected $model = FabricPDF::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'template_id' => Template::factory()->create()->id,
            'content' => json_encode(['foo' => 'bar'])
        ];
    }
}
