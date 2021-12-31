<?php

namespace EscolaLms\TemplatesPdf\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PdfListingRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return $this->user();
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
