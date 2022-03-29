<?php

namespace EscolaLms\TemplatesPdf\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class PdfReadRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {

        return Gate::allows('read', Template::class);
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
