<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\MediaRule;

class CreateClubPlayerRequest extends FormRequest
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
            'full_name' => 'required|min:2|alpha_spaceable',
            'player_position' => 'required|exists:player_positions,id',
            'club' => 'required|exists:clubs,id',
            'media' => [
                'bail',
                'required',
                'exists:media,id', 
                new MediaRule(),
            ],
        ];

        return $rules;
    }
}
