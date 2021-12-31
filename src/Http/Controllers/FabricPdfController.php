<?php

namespace EscolaLms\TemplatesPdf\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\TemplatesPdf\Http\Requests\PdfListingRequest;
use EscolaLms\TemplatesPdf\Http\Requests\PdfReadRequest;
use EscolaLms\TemplatesPdf\Http\Resources\PdfResource;
use EscolaLms\TemplatesPdf\Http\Resources\PdfListResource;
use EscolaLms\TemplatesPdf\Models\FabricPDF;
use Illuminate\Http\JsonResponse;
use EscolaLms\TemplatesPdf\Http\Controllers\Swagger\FabricPdfControllerSwagger;

class FabricPdfController extends EscolaLmsBaseController  implements FabricPdfControllerSwagger
{
    public function index(PdfListingRequest $request): JsonResponse
    {

        $pdfs = FabricPDF::where('user_id', auth()->user()->id)->paginate();
        return $this->sendResponseForResource(PdfListResource::collection($pdfs), "pdfs list retrieved successfully");
    }

    public function show(PdfReadRequest $request, int $id): JsonResponse
    {
        $pdf = FabricPDF::findOrFail($id);
        if ($pdf->user_id != auth()->user()->id) {
            return $this->sendError(sprintf("pdf with id '%s' not yours", $id), 403);
        }
        return $this->sendResponseForResource(PdfResource::make($pdf), "pdf fetched successfully");
    }
}
