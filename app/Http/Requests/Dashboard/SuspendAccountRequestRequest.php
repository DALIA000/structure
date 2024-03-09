<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class SuspendAccountRequestRequest extends FormRequest
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
    }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, mixed>
   */
  public function rules()
  {
    return [
        'starts_at' => 'required|date|after:yesterday',
        'ends_at' => 'required|date|after:starts_at',
        'note' => 'nullable|string',
    ];
  }
}
