<?php

namespace EscolaLms\TemplatesPdf\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\TemplatesPdf\Http\Controllers\Swagger\FabricPdfControllerSwagger;
use EscolaLms\TemplatesPdf\Http\Requests\PdfListingAdminRequest;
use EscolaLms\TemplatesPdf\Http\Requests\PdfListingRequest;
use EscolaLms\TemplatesPdf\Http\Requests\PdfReadRequest;
use EscolaLms\TemplatesPdf\Http\Resources\PdfListResource;
use EscolaLms\TemplatesPdf\Http\Resources\PdfResource;
use EscolaLms\TemplatesPdf\Models\FabricPDF;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use EscolaLms\TemplatesPdf\Services\Contracts\ReportBroServiceContract;
use Symfony\Component\HttpFoundation\BinaryFileResponse;



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
        return $this->sendResponseForResource(PdfResource::make($pdf), "pdf fetched successfully");
    }

    public function generate(PdfReadRequest $request, int $id): BinaryFileResponse
    {
        $service = App::make(ReportBroServiceContract::class);
        return response()->download($service->generateFileFromRecord($id, false));
    }

    public function admin(PdfListingAdminRequest $request): JsonResponse
    {
        if ($request->has('user_id')) {
            $pdfs = FabricPDF::where('user_id', $request->input('user_id'))->get();
        } else if ($request->has('template_id')) {
            $pdfs = FabricPDF::where('template_id', $request->input('template_id'))->get();
        } else {
            $pdfs = FabricPDF::paginate();
        }
        return $this->sendResponseForResource(PdfListResource::collection($pdfs), "pdfs list retrieved successfully");
    }

    public function reportBro(Request $request): mixed
    {
        $service = App::make(ReportBroServiceContract::class);
        $response = $service->passAll($request);


        if (strpos($response, "key") !== FALSE) {
            return response($response, 200);
        }

        return response()->download($response);
    }
}
