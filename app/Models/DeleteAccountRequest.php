<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeleteAccountRequest extends Model
{
    use HasFactory;

    protected $guarded =[];

    protected $casts = [
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_type_model()
    {
        return $this->belongsTo(UserType::class, 'user_type', 'user_type');
    }
}
