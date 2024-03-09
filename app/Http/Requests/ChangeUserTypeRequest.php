<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\{
    UserType,
    Country,
};
use App\Rules\IsDocumentAvailable;
use Illuminate\Validation\Rule;

class ChangeUserTypeRequest extends FormRequest
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
        $available_user_types = UserType::whereNotIn('slug', ['federation', 'club'])->pluck('id')->implode(',');

        $first_name = 'required|min:2|alpha_spaceable';
        $last_name = 'required|min:2|alpha_spaceable';
        $business_name = 'required|min:2|business_name';

        $academy_level = 'required|exists:academy_levels,id';

        $latitude = 'required|between:-90,90';
        $longitude = 'required|between:-180,180';

        $player_position = 'required|exists:player_positions,id';
        $player_footness = 'required|exists:player_footnesses,id';
        $academy = 'nullable|exists:users,username';

        $achievements = 'required|string';
        $trainer_experience_level = 'required|exists:trainer_experience_levels,id';
        
        $birthday = 'required|date';

        $certification = [
            'bail',
            'required',
            'exists:documents,id',
            new IsDocumentAvailable(),
        ];

        $rules = [
            'user_type' => 'required|exists:user_types,id|in:' . $available_user_types,
        ];

        $user_type = UserType::find($this->user_type);

        switch ($user_type?->slug) {
            case 'academy':
                $rules['birthday'] = $birthday;
                $rules['business_name'] = $business_name;
                $rules['academy_level'] = $academy_level;
                $rules['commercial_certification'] = $certification;
                $rules['latitude'] = $latitude;
                $rules['longitude'] = $longitude;
                break;

            case 'player':
                $rules['first_name'] = $first_name;
                $rules['last_name'] = $last_name;
                $rules['player_position'] = $player_position;
                $rules['player_footness'] = $player_footness;
                $rules['academy'] = $academy;
                break;

            case 'trainer':
                $rules['first_name'] = $first_name;
                $rules['last_name'] = $last_name;
                $rules['trainer_experience_level'] = $trainer_experience_level;
                $rules['achievements'] = $achievements;
                $rules['experience_certification'] = [...$certification, 'different:training_certification'];
                $rules['training_certification'] = [...$certification, 'different:experience_certification'];
                break;

            case 'influencer':
                $rules['first_name'] = $first_name;
                $rules['last_name'] = $last_name;
                $rules['influencement_certification'] = $certification;
                break;

            case 'journalist':
                $rules['first_name'] = $first_name;
                $rules['last_name'] = $last_name;
                $rules['journalism_certification'] = $certification;
                break;

            case 'business':
                $rules['birthday'] = $birthday;
                $rules['business_name'] = $business_name;
                $rules['commercial_certification'] = $certification;
                break;
        }

        return $rules;
    }
}
