<?php

namespace EscolaLms\TemplatesPdf\Http\Resources;

use EscolaLms\TemplatesPdf\Models\FabricPDF;
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
            'id' => $this->id,
            'template' => $this->template,
            'path' => $this->path,
            'user_id' => $this->user_id, 
            'user' => User::find($this->user_id), 
            'title' => $this->title,
            'content' => $this->content ? json_decode($this->content) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
