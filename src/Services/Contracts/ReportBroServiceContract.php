<?php

namespace EscolaLms\TemplatesPdf\Services\Contracts;

use Illuminate\Http\Request;

interface ReportBroServiceContract
{
    public function passAll(Request $request): string;
    public function generateFileFromRecord(int $id): string;
}
