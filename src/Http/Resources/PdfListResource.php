<?php

namespace EscolaLms\TemplatesPdf\Http\Resources;

use EscolaLms\TemplatesPdf\Models\FabricPDF;
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
            'id' => $this->id,
            'title' => $this->title,
            'template' => $this->template,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
