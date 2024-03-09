<?php

namespace App\Http\Requests;

use App\Models\UserType;
use App\Models\Academy;
use Illuminate\Foundation\Http\FormRequest;

class SubscribeCourseRequest extends FormRequest
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
        // TODO: where user_type_class is player, and belongs to academy
        $loggedinUser = app('loggedinUser');
        if ($loggedinUser->user_type_class == Academy::class) {
            return [
                'players' => 'required|array|min:1|distinct',
                'players.*' => 'required|string|exists:users,username',
            ];
        }

        return [];
    }
}
