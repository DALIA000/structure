<?php

namespace App\Observers;

use App\Models\UserPreference;

class UserPreferenceObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    // public $afterCommit = true;

    public function updated(UserPreference $userPreference) : void
    {
        // $userPreference->user->followers()->update(['is_pending' => 0]);
    }
}
