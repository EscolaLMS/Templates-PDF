<?php

namespace EscolaLms\TemplatesPdf\Courses;

class UserFinishedCourseVariables extends CommonUserAndCourseVariables
{
    public static function defaultSectionsContent(): array
    {
        return [
            'content' => ''
        ];
    }
}
