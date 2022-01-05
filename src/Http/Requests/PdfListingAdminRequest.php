<?php

namespace EscolaLms\TemplatesPdf\Http\Requests;

use EscolaLms\Templates\Models\Template;
use Illuminate\Foundation\Http\FormRequest;

class PdfListingAdminRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return $this->user() && $this->user()->can('list', Template::class);
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
        ];
    }
}
