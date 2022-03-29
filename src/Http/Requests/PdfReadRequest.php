<?php

namespace EscolaLms\TemplatesPdf\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use EscolaLms\TemplatesPdf\Models\FabricPDF;

class PdfReadRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {

        $pdf = FabricPDF::findOrFail($this->route('id'));

        return Gate::allows('read', $pdf);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
