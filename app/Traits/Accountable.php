<?php

namespace App\Traits;

use App\Models\{
    DeleteAccountRequest,
    User,
    Status,
    ModelStatus,
    UserType,
};

trait Accountable
{
    use Statusable;
    
    public function account()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    static public function userTypeId()
    {
        return UserType::where('user_type', SELF::class)->first()?->id;
    }

    public function delete_account_requests()
    {
        return $this->morphMany(DeleteAccountRequest::class, 'user');
    }
}
