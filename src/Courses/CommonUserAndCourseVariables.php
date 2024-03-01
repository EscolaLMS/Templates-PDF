<?php

namespace EscolaLms\TemplatesPdf\Courses;

use EscolaLms\Core\Models\User;
use EscolaLms\Courses\Models\Course;
use EscolaLms\Templates\Events\EventWrapper;
use EscolaLms\TemplatesPdf\Core\PdfVariables;
use EscolaLms\TemplatesPdf\Models\FabricPDF;
use Illuminate\Support\Carbon;

abstract class CommonUserAndCourseVariables extends PdfVariables
{
    const VAR_USER_NAME                             = '@VarUserName';
    const VAR_COURSE_TITLE                          = '@VarCourseTitle';
    const VAR_COURSE_SUBTITLE                       = '@VarCourseSubtitle';
    const VAR_COURSE_ACTIVE_FROM                    = '@VarCourseActiveFrom';
    const VAR_COURSE_ACTIVE_TO                      = '@VarCourseActiveTo';
    const VAR_COURSE_CATEGORIES                     = '@VarCourseCategories';
    const VAR_COURSE_CATEGORIES_WITH_BREADCRUMBS    = '@VarCourseCategoriesWithBreadcrumbs';
    const VAR_CATEGORIES_ID                         = '@VarCategoriesId';
    const VAR_COURSE_ID                             = '@VarCourseId';
    const VAR_CONTINUOUS_CERT_NUMBER                = '@VarContinuousCertNumber';
    const VAR_ANNUAL_CERT_NUMBER                    = '@VarAnnualCertNumber';

    public static function mockedVariables(?User $user = null): array
    {
        $faker = \Faker\Factory::create();
        return array_merge(parent::mockedVariables(), [
            self::VAR_USER_NAME                             => $faker->name(),
            self::VAR_COURSE_TITLE                          => $faker->word(),
            self::VAR_COURSE_SUBTITLE                       => $faker->word(),
            self::VAR_COURSE_ACTIVE_FROM                    => $faker->dateTime()->format('Y-m-d'),
            self::VAR_COURSE_ACTIVE_TO                      => $faker->dateTime()->format('Y-m-d'),
            self::VAR_COURSE_CATEGORIES                     => $faker->word(),
            self::VAR_COURSE_CATEGORIES_WITH_BREADCRUMBS    => $faker->word(),
            self::VAR_CATEGORIES_ID                         => $faker->numberBetween(1),
            self::VAR_COURSE_ID                             => $faker->numberBetween(1),
            self::VAR_CONTINUOUS_CERT_NUMBER                => $faker->numberBetween(1),
            self::VAR_ANNUAL_CERT_NUMBER                    => $faker->numberBetween(1) . '/' . now()->year,
        ]);
    }

    public static function variablesFromEvent(EventWrapper $event): array
    {
        return array_merge(parent::variablesFromEvent($event), [
            self::VAR_USER_NAME                             => $event->user()->name,
            self::VAR_COURSE_TITLE                          => $event->getCourse()->title,
            self::VAR_COURSE_SUBTITLE                       => $event->getCourse()->subtitle,
            self::VAR_COURSE_ACTIVE_FROM                    => $event->getCourse()->active_from ? Carbon::make($event->getCourse()->active_from)->format('Y-m-d') : '',
            self::VAR_COURSE_ACTIVE_TO                      => $event->getCourse()->active_to ? Carbon::make($event->getCourse()->active_to)->format('Y-m-d') : '',
            self::VAR_COURSE_CATEGORIES                     => $event->getCourse()->categories->pluck('name')->implode(', '),
            self::VAR_COURSE_CATEGORIES_WITH_BREADCRUMBS    => $event->getCourse()->categories->pluck('name_with_breadcrumbs')->implode(', '),
            self::VAR_CATEGORIES_ID                         => $event->getCourse()->categories->pluck('id')->implode(', '),
            self::VAR_COURSE_ID                             => (string) $event->getCourse()->id,
            self::VAR_CONTINUOUS_CERT_NUMBER                => (string) (FabricPDF::count() + 1),
            self::VAR_ANNUAL_CERT_NUMBER                    => FabricPDF::whereYear('created_at', now()->year)->count() + 1 . '/' . now()->year,
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
