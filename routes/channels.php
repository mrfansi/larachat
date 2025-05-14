<?php

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// Default Laravel user channel for notifications
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private chat channel between two users
Broadcast::channel('chat.{senderId}.{receiverId}', function (User $user, $senderId, $receiverId) {
    return (int) $user->id === (int) $senderId || (int) $user->id === (int) $receiverId;
});

// Presence channel for group chats
Broadcast::channel('group.{groupId}', function (User $user, $groupId) {
    $group = Group::find($groupId);
    if (! $group) {
        return false;
    }

    $isMember = $user->groups->contains($groupId);

    return $isMember ? [
        'id' => $user->id,
        'name' => $user->name,
        'initials' => $user->initials(),
    ] : false;
});

// Typing indicator channel (private)
Broadcast::channel('typing.{receiverId}', function (User $user, $receiverId) {
    return (int) $user->id !== (int) $receiverId;
});

// Typing indicator channel (group)
Broadcast::channel('typing.group.{groupId}', function (User $user, $groupId) {
    return $user->groups->contains($groupId) ? [
        'id' => $user->id,
        'name' => $user->name,
    ] : false;
});
