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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use EscolaLms\TemplatesPdf\Services\Contracts\ReportBroServiceContract;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FabricPdfController extends EscolaLmsBaseController implements FabricPdfControllerSwagger
{
    public function index(PdfListingRequest $request): JsonResponse
    {
        $pdfs = FabricPDF::query()
            ->where('user_id', auth()->user()->id)
            ->when($request->has('assignable_type') && $request->has('assignable_id'),
                fn(Builder $query) => $query
                    ->where('assignable_type', $request->get('assignable_type'))
                    ->where('assignable_id', $request->get('assignable_id'))
            )
            ->paginate($request->get('per_page') ?? 15);

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
            $pdfs = FabricPDF::paginate($request->get('per_page') ?? 15);
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
