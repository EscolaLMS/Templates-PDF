<?php

namespace EscolaLms\TemplatesPdf\Http\Resources;

use EscolaLms\TemplatesPdf\Models\FabricPDF;
use EscolaLms\TemplatesPdf\Parsers\VarsParser;
use Illuminate\Http\Resources\Json\JsonResource;
use EscolaLms\Auth\Models\User;


class PdfResource extends JsonResource
{
    public function __construct(FabricPDF $pdf)
    {
        $this->resource = $pdf;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'template' => $this->resource->template,
            'path' => $this->resource->path,
            'user_id' => $this->resource->user_id,
            'user' => User::find($this->resource->user_id),
            'title' => $this->resource->title,
            'content' => $this->resource->content ? json_decode($this->resource->content) : null,
            'vars' => VarsParser::parseVars($this->vars),
            'assignable_type' => $this->resource->assignable_type,
            'assignable_id' => $this->resource->assignable_id,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
