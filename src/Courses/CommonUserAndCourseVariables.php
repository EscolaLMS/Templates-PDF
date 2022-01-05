<?php

namespace EscolaLms\TemplatesPdf\Courses;

use EscolaLms\Core\Models\User;
use EscolaLms\Courses\Models\Course;
use EscolaLms\Templates\Events\EventWrapper;
use EscolaLms\TemplatesPdf\Core\PdfVariables;

abstract class CommonUserAndCourseVariables extends PdfVariables
{
    const VAR_USER_NAME       = '@VarUserName';
    const VAR_COURSE_TITLE    = '@VarCourseTitle';

    public static function mockedVariables(?User $user = null): array
    {
        $faker = \Faker\Factory::create();
        return array_merge(parent::mockedVariables(), [
            self::VAR_USER_NAME       => $faker->name(),
            self::VAR_COURSE_TITLE    => $faker->word(),
        ]);
    }

    public static function variablesFromEvent(EventWrapper $event): array
    {
        return array_merge(parent::variablesFromEvent($event), [
            self::VAR_USER_NAME    => $event->user()->name,
            self::VAR_COURSE_TITLE => $event->getCourse()->title,
        ]);
    }

    public static function requiredVariables(): array
    {
        return [
            // self::VAR_USER_NAME,
            // self::VAR_COURSE_TITLE,
        ];
    }

    public static function requiredVariablesInSection(string $sectionKey): array
    {
        switch ($sectionKey) {
            case 'title':
                return [
                    self::VAR_COURSE_TITLE,
                ];
            case 'content':
                return [
                    self::VAR_USER_NAME,
                    self::VAR_COURSE_TITLE,
                ];
            default:
                return [];
        }
    }

    public static function assignableClass(): ?string
    {
        return Course::class;
    }
}
