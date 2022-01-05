<?php

namespace EscolaLms\TemplatesPdf\Core;

use EscolaLms\Core\Models\User;
use EscolaLms\Templates\Contracts\TemplateVariableContract;
use EscolaLms\Templates\Core\AbstractTemplateVariableClass;
use EscolaLms\Templates\Events\EventWrapper;

abstract class PdfVariables extends AbstractTemplateVariableClass implements TemplateVariableContract
{
    const VAR_APP_NAME       = '@VarAppName';

    public static function mockedVariables(?User $user = null): array
    {
        return [
            self::VAR_APP_NAME => config('app.name')
        ];
    }

    public static function variablesFromEvent(EventWrapper $event): array
    {
        return [
            self::VAR_APP_NAME => config('app.name')
        ];
    }

    public static function requiredSections(): array
    {
        return [];
    }
}
