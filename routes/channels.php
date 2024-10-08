<?php

use Illuminate\Support\Facades\Broadcast;

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

//Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//    return (int) $user->id === (int) $id;
//});

//Broadcast::channel('conversation-{conversation_id}', function ($user, $conversation_id) {
//    return $user->conversations->contains('id', $conversation_id);
//});

Broadcast::channel('online', function ($user) {
    if (auth()->check()) {
        return ['id' => $user->id, 'name' => $user->name];
    };
    return false;
});

Broadcast::channel('user-{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
