<?php

namespace EscolaLms\TemplatesPdf\Courses;

use Illuminate\Support\Facades\Lang;

class UserFinishedCourseVariables extends CommonUserAndCourseVariables
{
    public static function defaultSectionsContent(): array
    {
        return [
            'title' => Lang::get('Certificate for :course', ['course' => self::VAR_COURSE_TITLE]),
            'content' => <<<JSON
            {"version":"4.6.0","objects":[{"type":"text","version":"4.6.0","left":200,"top":100,"width":429.09,"height":45.2,"fill":"#000000","fontFamily":"helvetica","fontWeight":"","text":"Certificate of completion","styles":{}},{"type":"text","version":"4.6.0","left":269,"top":186,"width":248.58,"height":28.25,"fill":"#000000","fontFamily":"helvetica","fontWeight":"","fontSize":25,"text":"User: @VarUserName","styles":{}},{"type":"text","version":"4.6.0","left":257,"top":254,"width":283.83,"height":28.25,"fill":"#000000","fontFamily":"helvetica","fontWeight":"","fontSize":25,"text":"Course: @VarCourseTitle","styles":{}}]}
            JSON,
        ];
    }
}
