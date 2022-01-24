<?php

namespace EscolaLms\TemplatesPdf\Core;

use EscolaLms\Core\Models\User;
use EscolaLms\Templates\Contracts\TemplateVariableContract;
use EscolaLms\Templates\Core\AbstractTemplateVariableClass;
use EscolaLms\Templates\Core\SettingsVariables;
use EscolaLms\Templates\Events\EventWrapper;

abstract class PdfVariables extends AbstractTemplateVariableClass implements TemplateVariableContract
{
    public static function mockedVariables(?User $user = null): array
    {
        return SettingsVariables::getSettingsValues();
    }

    public static function variablesFromEvent(EventWrapper $event): array
    {
        return SettingsVariables::getSettingsValues();
    }

    public static function requiredSections(): array
    {
        return [];
    }
}
