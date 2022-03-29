<?php

namespace EscolaLms\TemplatesPdf\Http\Requests;

use EscolaLms\TemplatesPdf\Models\FabricPDF;
use Illuminate\Foundation\Http\FormRequest;

class PdfListingAdminRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('list', FabricPDF::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => ['sometimes', 'integer'],
            'template_id' => ['sometimes', 'integer'],
        ];
    }
}
