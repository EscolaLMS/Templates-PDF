<?php

namespace EscolaLms\TemplatesPdf\Services;

use EscolaLms\TemplatesPdf\Services\Contracts\ReportBroServiceContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use EscolaLms\TemplatesPdf\Models\FabricPDF;
use EscolaLms\TemplatesPdf\EscolaLmsTemplatesPdfServiceProvider;

class ReportBroService implements ReportBroServiceContract
{
    public function getKeyFromPayload(string $payload): string
    {

        $reportBroUrl = config(EscolaLmsTemplatesPdfServiceProvider::CONFIG_KEY . '.reportbro_url');

        $response = Http::withBody($payload, 'application/json')
            ->send('PUT', $reportBroUrl);

        $fullKey = $response->getBody()->getContents();

        return $fullKey;
    }

    public function getFilepathFromKey(string $key): string
    {
        $reportBroUrl = config(EscolaLmsTemplatesPdfServiceProvider::CONFIG_KEY . '.reportbro_url');
        $tempName = tempnam(sys_get_temp_dir(), 'response') . '.pdf';

        Http::sink($tempName)->get($reportBroUrl, ['key' => $key, 'outputFormat' => 'pdf']);

        return $tempName;
    }

    private function generateFromPayload(string $payload): string
    {

        $fullKey = $this->getKeyFromPayload($payload);
        $keys = explode(":", $fullKey);

        $tempName = $this->getFilepathFromKey($keys[1]);

        return $tempName;
    }
    public function generateFileFromRecord(int $id, $testData = true): string
    {
        $record = FabricPDF::findOrFail($id)->toArray();

        $vars = [];
        if (is_array($record['vars'])) {
            foreach ($record['vars'] as $key => $value) {
                $vars[str_replace('@', '', $key)] = $value;
            }
        }

        $payload = '
        {
            "data": ' . json_encode($vars) . ',
            "isTestData": ' . ($testData ? "true" : "false") . ',
            "outputFormat": "pdf",
            "report": ' . $record["content"] . '
        }';

        return $this->generateFromPayload($payload);
    }

    public function passAll(Request $request): string
    {

        switch ($request->method()) {
            case "PUT":
                return $this->getKeyFromPayload($request->getContent());
            case "GET":
            default:
                return $this->getFilepathFromKey($request->get('key'));
        }
    }
}
