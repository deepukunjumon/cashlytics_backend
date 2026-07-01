<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// UUID primary keys throughout this app — (int) casting a UUID always yields 0,
// which would make this comparison true for any two users. Must compare as strings.
Broadcast::channel('App.Models.User.{id}', function (User $user, string $id) {
    return hash_equals((string) $user->id, $id);
});
