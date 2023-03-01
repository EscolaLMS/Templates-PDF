<?php

namespace EscolaLms\TemplatesPdf\Core;

use EscolaLms\Core\Models\User;
use EscolaLms\Templates\Contracts\TemplateVariableContract;
use EscolaLms\Templates\Core\AbstractTemplateVariableClass;
use EscolaLms\Templates\Events\EventWrapper;

abstract class PdfVariables extends AbstractTemplateVariableClass implements TemplateVariableContract
{
    const VAR_APP_NAME       = '@VarAppName';
    const VAR_TODAY = '@VarToday';

    public static function mockedVariables(?User $user = null): array
    {
        $faker = \Faker\Factory::create();
        return [
            self::VAR_APP_NAME => config('app.name'),
            self::VAR_TODAY => today()->format('d.m.Y'),
        ];
    }

    public static function variablesFromEvent(EventWrapper $event): array
    {
        return [
            self::VAR_APP_NAME => config('app.name'),
            self::VAR_TODAY => today()->format('d.m.Y'),
        ];
    }

    public static function requiredSections(): array
    {
        return [];
    }
}
