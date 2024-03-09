<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\MediaRule;
use App\Models\ClubPlayer;

class EditClubPlayerRequest extends FormRequest
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
            'full_name' => 'notNullable|min:2|alpha_spaceable',
            'player_position' => 'notNullable|exists:player_positions,id',
            'club' => 'notNullable|exists:clubs,id',
            'media' => [
                'bail',
                'nullable',
                'exists:media,id', 
                new MediaRule(ClubPlayer::class),
            ],
        ];

        return $rules;
    }
}
