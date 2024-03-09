<?php

namespace App\Listeners;

use App\Models\{
    Like,
    Comment,
    Save,
    Video,
    User,
};
use App\Notifications\MessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetBlockedkUserRestrictions 
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $blockable = collect($event->participants)->filter(fn ($participant) => $participant)->first();
        $user = $event->user;

        // likes
        Like::where(function ($query) {$query->where('likable_type', Video::class)->orWhere('likable_type', Comment::class);})
            ->where(function ($query) use ($user, $blockable) {
                $query->where(function ($query) use ($user, $blockable) {
                    $query->where('user_id', $user->id)
                        ->whereHas('likable', function ($query) use ($user, $blockable) {
                            $query->where('user_id', $blockable->id);
                    });
                })->orWhere(function ($query) use ($user, $blockable) {
                    $query->where('user_id', $blockable->id)
                        ->whereHas('likable', function ($query) use ($user, $blockable) {
                            $query->where('user_id', $user->id);
                    });
                });
        })->delete();

        // comments
        Comment::where(function ($query) {$query->where('commentable_type', Video::class)->orWhere('commentable_type', Comment::class);})
            ->where(function ($query) use ($user, $blockable) {
                $query->where(function ($query) use ($user, $blockable) {
                    $query->where('user_id', $user->id)
                        ->whereHas('commentable', function ($query) use ($user, $blockable) {
                            $query->where('user_id', $blockable->id);
                    });
                })->orWhere(function ($query) use ($user, $blockable) {
                    $query->where('user_id', $blockable->id)
                        ->whereHas('commentable', function ($query) use ($user, $blockable) {
                            $query->where('user_id', $user->id);
                    });
                });
        })->delete();

        // saves
        // accounts
        Save::where(function ($query) {$query->where('savable_type', User::class);})
            ->where(function ($query) use ($user, $blockable) {
                $query->where(function ($query) use ($user, $blockable) {
                    $query->where('user_id', $user->id)
                        ->whereHas('savable', function ($query) use ($user, $blockable) {
                            $query->where('id', $blockable->id);
                    });
                })->orWhere(function ($query) use ($user, $blockable) {
                    $query->where('user_id', $blockable->id)
                        ->whereHas('savable', function ($query) use ($user, $blockable) {
                            $query->where('id', $user->id);
                    });
                });
        })->delete();

        // other
        Save::where(function ($query) {$query->where('savable_type', Video::class)->orWhere('savable_type', Comment::class);})
            ->where(function ($query) use ($user, $blockable) {
                $query->where(function ($query) use ($user, $blockable) {
                    $query->where('user_id', $user->id)
                        ->whereHas('savable', function ($query) use ($user, $blockable) {
                            $query->where('user_id', $blockable->id);
                    });
                })->orWhere(function ($query) use ($user, $blockable) {
                    $query->where('user_id', $blockable->id)
                        ->whereHas('savable', function ($query) use ($user, $blockable) {
                            $query->where('user_id', $user->id);
                    });
                });
        })->delete();
    }
}
