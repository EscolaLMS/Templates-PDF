<?php

namespace EscolaLms\TemplatesPdf\Services;

use EscolaLms\TemplatesPdf\Services\Contracts\ReportBroServiceContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use EscolaLms\TemplatesPdf\Models\FabricPDF;

class ReportBroService implements ReportBroServiceContract
{

    private function generateFromPayload(string $payload): string
    {
        $reportBroUrl = config("reportbroUrl", "https://reportbro.stage.etd24.pl/reportbro/report/run");

        $response = Http::withBody($payload, 'application/json')
            ->send('PUT', $reportBroUrl);

        $fullKey = $response->getBody()->getContents();

        $keys = explode(":", $fullKey);
        $tempName = tempnam(sys_get_temp_dir(), 'response') . '.pdf';

        Http::sink($tempName)->get($reportBroUrl, ['key' => $keys[1], 'outputFormat' => 'pdf']);

        return $tempName;
    }
    public function generateFileFromRecord(int $id): string
    {
        $record = FabricPDF::findOrFail($id)->toArray();

        foreach ($record['vars'] as $key => $value) {
            $vars[str_replace('@', '', $key)] = $value;
        }

        $payload = '
        { 
            "data": ' . json_encode($vars) . ',
            "isTestData": false, 
            "outputFormat": "pdf",
            "report": ' . $record["content"] . '
        }';

        return $this->generateFromPayload($payload);
    }

    public function passAll(Request $request): string
    {
        return $this->generateFromPayload($request->getContent());
    }
}
