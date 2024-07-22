<?php

namespace EscolaLms\TemplatesPdf\Core;

use EscolaLms\Core\Models\User;
use EscolaLms\Templates\Contracts\TemplateChannelContract;
use EscolaLms\Templates\Core\AbstractTemplateChannelClass;
use EscolaLms\Templates\Core\SettingsVariables;
use EscolaLms\Templates\Core\TemplateSectionSchema;
use EscolaLms\Templates\Enums\TemplateSectionTypeEnum;
use EscolaLms\Templates\Events\EventWrapper;
use EscolaLms\Templates\Models\Template;
use EscolaLms\Templates\Models\TemplateSection;
use EscolaLms\TemplatesPdf\Models\FabricPDF;
use Illuminate\Support\Collection;
use EscolaLms\Templates\Facades\Template as TemplateFacade;
use ReflectionClass;
use ReflectionProperty;

class PdfChannel extends AbstractTemplateChannelClass implements TemplateChannelContract
{
    public static function send(EventWrapper $event, array $sections): bool
    {
        $varsService = TemplateFacade::getVariableClassName($event->eventClass(), PdfChannel::class);
        $vars = array_merge(SettingsVariables::getSettingsValues(), $varsService::variablesFromEvent($event));

        FabricPDF::create([
            'user_id' => $event->user()->id,
            'template_id' => $sections['template_id'],
            'title' => $sections['title'],
            'content' => $sections['content'],
            'vars' => $vars,
            'assignable_type' => $varsService::assignableClass(),
            'assignable_id' => $varsService::assignableClass() ? $event->extractIdForPropertyOfClass($varsService::assignableClass()) : null,
        ]);

        return true;
    }

    public static function preview(User $user, array $sections): bool
    {
        return true;
    }

    public static function sections(): Collection
    {
        return new Collection([
            // @phpstan-ignore-next-line
            new TemplateSectionSchema('title', TemplateSectionTypeEnum::SECTION_TEXT(), true),
            // @phpstan-ignore-next-line
            new TemplateSectionSchema('content', TemplateSectionTypeEnum::SECTION_FABRIC(), true),
        ]);
    }

    public static function processTemplateAfterSaving(Template $template): Template
    {
        $content = $template->sections()->where('key', 'content')->first()->content;

        TemplateSection::updateOrCreate(['template_id' => $template->getKey(), 'key' => 'content'], ['content' => $content]);

        return $template->refresh();
    }
}
