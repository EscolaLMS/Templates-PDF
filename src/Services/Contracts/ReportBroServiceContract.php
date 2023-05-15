<?php

namespace EscolaLms\TemplatesPdf\Services\Contracts;

use Illuminate\Http\Request;

interface ReportBroServiceContract
{
    public function getKeyFromPayload(string $payload): string;
    public function getFilepathFromKey(string $key): string;
    public function passAll(Request $request): string;
<<<<<<< HEAD
    public function generateFileFromRecord(int $id, bool $testData): string;
=======
    public function generateFileFromRecord(int $id): string;
>>>>>>> 491a36e0830401532643dbac02a778f5f5d37d07
}
