<?php

namespace EscolaLms\TemplatesPdf\Core;

use EscolaLms\Core\Models\User;
use EscolaLms\Templates\Events\EventWrapper;
use Illuminate\Support\Facades\Lang;

class UserVariables extends PdfVariables
{
    const VAR_USER_NAME = '@VarUserName';
    const VAR_COURSE_TITLE = '@VarCourseTitle';
    const VAR_PRODUCT_TITLE = '@VarProductTitle';

    public static function mockedVariables(?User $user = null): array
    {
        $faker = \Faker\Factory::create();
        return array_merge(parent::mockedVariables(), [
            self::VAR_USER_NAME => $faker->name(),
            self::VAR_COURSE_TITLE => $faker->title,
            self::VAR_PRODUCT_TITLE => $faker->title,
        ]);
    }

    public static function variablesFromEvent(EventWrapper $event): array
    {
        return array_merge(parent::variablesFromEvent($event), [
            self::VAR_USER_NAME => $event->user()->name,
            self::VAR_COURSE_TITLE => $event->getCourse() ? $event->getCourse()->title : null,
            self::VAR_PRODUCT_TITLE => $event->getProduct() ? $event->getProduct()->name : null,
        ]);
    }

    public static function requiredVariables(): array
    {
        return [];
    }

    public static function requiredVariablesInSection(string $sectionKey): array
    {
        return [];
    }

    public static function assignableClass(): ?string
    {
        return null;
    }

    public static function defaultSectionsContent(): array
    {
        return [
            'title' => Lang::get('Pdf for :user', ['user' => self::VAR_USER_NAME]),
            'content' => <<<'JSON'
            {"docElements":[],"parameters":[{"id":1,"name":"page_count","type":"number","arrayItemType":"string","eval":false,"nullable":false,"pattern":"","expression":"","showOnlyNameType":true,"testData":"","testDataBoolean":false,"testDataImage":"","testDataImageFilename":""},{"id":2,"name":"page_number","type":"number","arrayItemType":"string","eval":false,"nullable":false,"pattern":"","expression":"","showOnlyNameType":true,"testData":"","testDataBoolean":false,"testDataImage":"","testDataImageFilename":""}],"styles":[],"version":4,"documentProperties":{"pageFormat":"A4","pageWidth":"","pageHeight":"","unit":"mm","orientation":"portrait","contentHeight":"","marginLeft":"","marginTop":"","marginRight":"","marginBottom":"","header":true,"headerSize":"80","headerDisplay":"always","footer":true,"footerSize":"80","footerDisplay":"always","patternLocale":"en","patternCurrencySymbol":"$","patternNumberGroupSymbol":""}}
            JSON,
        ];
    }
}
