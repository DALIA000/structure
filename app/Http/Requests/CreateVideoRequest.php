<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\{
    Video,
};
use Illuminate\Validation\Rule;
use App\Rules\MediaRule;

class CreateVideoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
    }

    public function rules()
    {
        $rules = [
            'status' => 'required|in:1,7',
            'comments_status' => 'required|in:8,9',
            'description' => 'nullable|string',
            'video' => 'required|mimetypes:video/x-ms-asf,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/avi,image/png,image/jpeg,image/jpg,image/webp',
            'taged_users' => 'array|min:0|max:10',
            'taged_users.*' => 'exists:users,username',
            'text' => 'nullable|array',
            'text.str' => 'required_with:text',
            'sticker' => 'nullable|array',
            'sticker.id' => 'required_with:sticker|exists:stickers,id',
        ];

        return $rules;
    }
}
