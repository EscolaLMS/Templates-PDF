<?php

namespace EscolaLms\TemplatesPdf\Parsers;

use Illuminate\Support\Str;

class VarsParser
{
    public static function parseVars(?array $vars = []): array
    {
        return collect($vars)
            ->filter(fn($var, $key) => !Str::contains($key, 'Global'))
            ->mapWithKeys(fn($var, $key) => [Str::snake(Str::replace("@", '', $key)) => $var])
            ->toArray();
    }
}
