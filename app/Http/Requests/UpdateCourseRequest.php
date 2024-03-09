<?php

namespace App\Http\Requests;

use App\Rules\MediaRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
    }

    public function rules()
    {
        return [
            'title' => 'string|min:3|max:255',
            'description' => 'string|min:10|max:500',
            'individual_price' => 'decimal:0|min:1|max:10000',
            'group_discount' => 'decimal:0|min:0|max:99',
            'seats_count' => 'integer|min:1|max:50',
            
            'video' => 'mimetypes:video/x-ms-asf,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/avi',
            
            'sessions' => 'array|min:1|max:4|distinct',
            'sessions.*' => 'array|min:1|max:2|distinct',
            'sessions.*.title' => 'required|string|min:1|max:100',
            'sessions.*.date' => 'date_format:Y-m-d|after:today',
            'sessions.*.time' => 'date_format:H:i',
        ];
    }
}
