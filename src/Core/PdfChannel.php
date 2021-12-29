<?php

namespace EscolaLms\TemplatesPdf\Core;

use EscolaLms\Core\Models\User;
use EscolaLms\Templates\Contracts\TemplateChannelContract;
use EscolaLms\Templates\Core\AbstractTemplateChannelClass;
use EscolaLms\Templates\Core\TemplateSectionSchema;
use EscolaLms\Templates\Enums\TemplateSectionTypeEnum;
use EscolaLms\Templates\Events\EventWrapper;
use EscolaLms\Templates\Models\Template;
use EscolaLms\Templates\Models\TemplateSection;
use HTMLPurifier_Config;
use HTMLPurifier;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PdfChannel extends AbstractTemplateChannelClass implements TemplateChannelContract
{
    public static function send(EventWrapper $event, array $sections): bool
    {
        if (!Arr::has($sections, self::sectionsRequired()) || !array_key_exists('contentHtml', $sections)) {
            return false;
        }

        dd($event, $sections);

        return true;
    }

    public static function preview(User $user, array $sections): bool
    {
        if (!Arr::has($sections, self::sectionsRequired()) || !array_key_exists('contentHtml', $sections)) {
            Log::error('Missing pdf sections in preview', $sections);
            return false;
        }

        dd($user, $sections);

        return true;
    }



    private static function fixHtml(string $html): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        return $purifier->purify($html);
    }

    public static function sections(): Collection
    {
        return new Collection([
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
