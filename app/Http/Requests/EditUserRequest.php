<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\UserType;
use App\Rules\IsDocumentAvailable;
use App\Rules\IsMediaAvailable;

class EditUserRequest extends FormRequest
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
        if ($this->exists('phone') && preg_match('/^([0-9\s\-\+\(\)]*)$/', $this->phone)) {
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
    $user = app('loggedinUser');

    $first_name = 'min:2|alpha_spaceable';
    $last_name = 'min:2|alpha_spaceable';
    $business_name = 'min:2|business_name';

    $academy_level = 'exists:academy_levels,id';

    $latitude = 'between:-90,90';
    $longitude = 'between:-180,180';

    $player_position = 'exists:player_positions,id';
    $player_footness = 'exists:player_footnesses,id';
    $academy = 'nullable|exists:users,username';

    $achievements = 'string';
    $description = 'nullable|string|max:500';
    $trainer_experience_level = 'exists:trainer_experience_levels,id';

    $certification = [
        'bail',
        'required',
        'exists:documents,id',
        new IsDocumentAvailable(User::class, $user->id),
    ];

    $rules = [
        'username' => 'min:2|slug|unique:users,username,'.$user->id,
        'email' => 'notNullable|emailRegex|unique:users,email,'.$user->id,
        'phone' => 'notNullable|phone|unique:users,phone,'.$user->id,
        'password' => 'min:8|passwordRegex',
        'password_confirmation' => 'same:password',
        'birthday' => 'notNullable|date',
        'city' => 'notNullable|exists:cities,slug', 
        'club' => 'nullable|exists:users,username', 
        'bio' => 'nullable|string|max:255',
        'image' => [
            'bail',
            'nullable',
            'exists:files,id',
            new IsMediaAvailable(User::class, $user->id),
        ],
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

        case 'club':
            $rules['description'] = $description;
            $rules['latitude'] = $latitude;
            $rules['longitude'] = $longitude;
            break;
    }
    
    return $rules;
  }
}
