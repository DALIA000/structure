<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\{
    UserType,
    Country,
};
use App\Rules\IsDocumentAvailable;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

    public function prepareForValidation()
    {
        if (preg_match('/^([0-9\s\-\+\(\)]*)$/', $this->phone)) {
            $phone = remove_special_chars($this->phone);
            if (!strlen($phone)) {
                $this->merge(['phone' => null]);
            } else {
                $this->merge(['phone' => '+' . $phone]);
            }
        }
    }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, mixed>
   */
  public function rules()
  {
    $first_name = 'required|min:2|alpha_spaceable';
    $last_name = 'required|min:2|alpha_spaceable';
    $business_name = 'required|min:2|business_name';

    $academy_level = 'required|exists:academy_levels,id';

    $latitude = 'required|between:-90,90';
    $longitude = 'required|between:-180,180';

    $player_position = 'required|exists:player_positions,id';
    $player_footness = 'required|exists:player_footnesses,id';
    $academy = 'nullable|string';

    $achievements = 'required|string';
    $trainer_experience_level = 'required|exists:trainer_experience_levels,id';

    // TODO: check id document is attached to another model
    $certification = [
        'bail',
        'required',
        'exists:documents,id',
        new IsDocumentAvailable,
    ];

    $rules = [
        'username' => 'required|slug|unique:users,username',
        'email' => 'required|emailRegex|unique:users,email',
        'phone' => 'required|phone|unique:users,phone',
        'password' => 'required|min:8|passwordRegex',
        'password_confirmation' => 'required|same:password',
        'birthday' => 'required|date',
        'country' => 'required|exists:countries,slug', 
        'city' => [
            'required',
            Rule::exists('cities', 'slug')->where(function ($query) {
                $country = Country::where('slug', $this->country)?->first();
                $query->where('country_id', $country?->id);
            }),
        ], 

        'user_type' => 'required|exists:user_types,id',
    ];

    $user_type = UserType::find($this->user_type);

    switch ($user_type?->slug) {
        case 'fan':
            $rules['first_name'] = $first_name;
            $rules['last_name'] = $last_name;
            break;

        case 'academy':
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

        case 'journalist':
            $rules['first_name'] = $first_name;
            $rules['last_name'] = $last_name;
            $rules['journalism_certification'] = $certification;
            break;

        case 'business':
            $rules['business_name'] = $business_name;
            break;
    }

    return $rules;
  }
}
