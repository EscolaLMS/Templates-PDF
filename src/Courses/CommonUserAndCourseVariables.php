<?php

namespace EscolaLms\TemplatesPdf\Courses;

use EscolaLms\Core\Models\User;
use EscolaLms\Courses\Models\Course;
use EscolaLms\Templates\Events\EventWrapper;
use EscolaLms\TemplatesPdf\Core\PdfVariables;
use Illuminate\Support\Carbon;

abstract class CommonUserAndCourseVariables extends PdfVariables
{
    const VAR_USER_NAME             = '@VarUserName';
    const VAR_COURSE_TITLE          = '@VarCourseTitle';
    const VAR_COURSE_SUBTITLE       = '@VarCourseSubtitle';
    const VAR_COURSE_ACTIVE_FROM    = '@VarCourseActiveFrom';
    const VAR_COURSE_ACTIVE_TO      = '@VarCourseActiveTo';
    const VAR_COURSE_CATEGORIES     = '@VarCourseCategories';

    public static function mockedVariables(?User $user = null): array
    {
        $faker = \Faker\Factory::create();
        return array_merge(parent::mockedVariables(), [
            self::VAR_USER_NAME             => $faker->name(),
            self::VAR_COURSE_TITLE          => $faker->word(),
            self::VAR_COURSE_SUBTITLE       => $faker->word(),
            self::VAR_COURSE_ACTIVE_FROM    => $faker->word(),
            self::VAR_COURSE_ACTIVE_TO      => $faker->dateTime()->format('Y-m-d'),
            self::VAR_COURSE_CATEGORIES     => $faker->dateTime()->format('Y-m-d'),
        ]);
    }

    public static function variablesFromEvent(EventWrapper $event): array
    {
        return array_merge(parent::variablesFromEvent($event), [
            self::VAR_USER_NAME             => $event->user()->name,
            self::VAR_COURSE_TITLE          => $event->getCourse()->title,
            self::VAR_COURSE_SUBTITLE       => $event->getCourse()->subtitle,
            self::VAR_COURSE_ACTIVE_FROM    => $event->getCourse()->active_from ? Carbon::make($event->getCourse()->active_from)->format('Y-m-d') : '',
            self::VAR_COURSE_ACTIVE_TO      => $event->getCourse()->active_to ? Carbon::make($event->getCourse()->active_to)->format('Y-m-d') : '',
            self::VAR_COURSE_CATEGORIES     => $event->getCourse()->categories->pluck('name')->implode(', '),
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
