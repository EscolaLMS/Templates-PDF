<?php

namespace EscolaLms\TemplatesPdf\Http\Resources;

use EscolaLms\TemplatesPdf\Models\FabricPDF;
use EscolaLms\TemplatesPdf\Parsers\VarsParser;
use Illuminate\Http\Resources\Json\JsonResource;

class PdfListResource extends JsonResource
{
    public function __construct(FabricPDF $pdf)
    {
        $this->resource = $pdf;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'template' => $this->resource->template,
            'user_id' => $this->resource->user_id,
            'vars' => VarsParser::parseVars($this->resource->vars),
            'assignable_type' => $this->resource->assignable_type,
            'assignable_id' => $this->resource->assignable_id,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
