<?php

namespace EscolaLms\TemplatesPdf\Http\Controllers\Swagger;

use EscolaLms\TemplatesPdf\Http\Requests\PdfListingRequest;
use EscolaLms\TemplatesPdf\Http\Requests\PdfReadRequest;
use EscolaLms\TemplatesPdf\Http\Requests\PdfListingAdminRequest;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface FabricPdfControllerSwagger
{
    /**
     * @OA\Get(
     *      path="/api/pdfs",
     *      summary="Get Fabric.js PDFs",
     *      tags={"Template PDFs"},
     *      description="Get paginated list of PDFs generated by templates",
     *      security={
     *         {"passport": {}},
     *      },
     *      @OA\Parameter(
     *          name="page",
     *          description="Pagination Page Number",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="number",
     *               default=1,
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          description="Pagination Per Page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="number",
     *               default=15,
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json"
     *          ),
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/FabricPDF")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(PdfListingRequest $request): JsonResponse;

    /**
     * @OA\Get(
     *      path="/api/pdfs/{id}",
     *      summary="Display the specified Fabric.js PDF",
     *      tags={"Template PDFs"},
     *      description="Get Fabric.js PDF",
     *      security={
     *         {"passport": {}},
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          description="id of Fabric.js PDF",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json"
     *          ),
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/components/schemas/FabricPDF"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show(PdfReadRequest $request, int $id): JsonResponse;

    /**
     * @OA\Get(
     *      path="/api/admin/pdfs",
     *      summary="Display the specified Fabric.js PDF",
     *      tags={"Admin Template PDFs"},
     *      description="Get Fabric.js PDF, IF user_id or template_id is provided all records are returned, otherwise list is paginated ",
     *      security={
     *         {"passport": {}},
     *      },
     *      @OA\Parameter(
     *          name="user_id",
     *          description="either user_id",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *          in="query"
     *      ),
     *      @OA\Parameter(
     *          name="template_id",
     *          description="or user_id",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *          in="query"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json"
     *          ),
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/components/schemas/FabricPDF"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function admin(PdfListingAdminRequest $request): JsonResponse;

    /**
     * @OA\Get(
     *      path="/api/pdfs/generate/{id}",
     *      summary="Generate Specific ReportBro PDF",
     *      tags={"Template PDFs"},
     *      description="Generate Specific ReportBro PDF",
     *      security={
     *         {"passport": {}},
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          description="id of Fabric.js/ReportBro PDF",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/pdf "
     *          )
     *      )
     * )
     */
    public function generate(PdfReadRequest $request, int $id): BinaryFileResponse;

    /**
     * @OA\Get(
     *      path="/reportbro/report/run",
     *      summary="Proxy for ReportBro server",
     *      tags={"Template PDFs"},
     *      description="Generate Specific ReportBro PDF",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of Fabric.js/ReportBro PDF",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/pdf "
     *          )
     *      )
     * )
     */
    public function reportBro(Request $request): mixed;
}
