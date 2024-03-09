<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;
use Musonza\Chat\Models\Message;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chats.{message_id}', function (User $user, int $message_id) {
    return $user->id /* === Message::find($message_id)->user_id */;
});

Broadcast::channel('chats', function (User $user) {
    return true /* === Message::find($message_id)->user_id */;
});