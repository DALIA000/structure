<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMediaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
          'file' => 'required|mimetypes:image/png,image/jpeg,image/jpg,image/webp,image/svg,video/mp4',
        ];
    }


    public function messages()
    {
        return [
          'file.mimetypes' => trans('messages.the :attribute must be a file of type: ') . 'png, jpeg, jpg, webp, svg ,mp4',
        ];
    }
}
