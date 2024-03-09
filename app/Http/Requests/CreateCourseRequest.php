<?php

namespace App\Http\Requests;

use App\Rules\MediaRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateCourseRequest extends FormRequest
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
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:10|max:500',
            'individual_price' => 'required|decimal:0|min:1|max:10000',
            'group_discount' => 'required|decimal:0|min:0|max:99',
            'seats_count' => 'required|integer|min:1|max:50',

            // 'video' => 'required|mimetypes:video/mp4',
            'video' => 'required|mimetypes:video/x-ms-asf,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/avi,image/png,image/jpeg,image/jpg,image/webp',
            
            'sessions' => 'required|array|min:1|max:10|distinct',
            'sessions.*' => 'required|array|min:1|max:3|distinct',
            'sessions.*.title' => 'required|string|min:1|max:100',
            'sessions.*.date' => 'required|date_format:Y-m-d|after:today',
            'sessions.*.time' => 'required|date_format:H:i',
        ];
    }
}
