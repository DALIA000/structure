<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
          'file' => 'required|mimetypes:image/png,image/jpeg,image/jpg,image/webp,application/pdf',
        ];
    }

    public function messages()
    {
        return [
          'file.mimetypes' => trans('messages.the :attribute must be a file of type: ') . 'png, jpeg, jpg, webp, pdf',
        ];
    }
}
