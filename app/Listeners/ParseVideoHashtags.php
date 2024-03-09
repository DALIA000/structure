<?php

namespace App\Listeners;

use App\Models\Model;
use App\Notifications\AppNotification;
use App\Notifications\MessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Models\{
    Hashtag,
    Video
};

class ParseVideoHashtags implements ShouldQueue
{
    public function __construct()
    {
    }

    public function handle($event)
    {
        $description = $event->video?->description;
        preg_match_all('/(?:^|\s)#(\w+)/', $description, $matches);

        foreach ($matches[1] as $value) {
            Hashtag::create([
                'hashtag' => $value,
                'hashtagable_id' => $event->video?->id,
                'model_id' => Model::where('type', Video::class)->first()?->id,
            ]);
        }
    }
}
