<?php

namespace App\Events;

use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The message instance.
     *
     * @var \App\Models\Message
     */
    public $message;

    /**
     * The user who sent the message
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The group the message was sent to
     *
     * @var \App\Models\Group
     */
    public $group;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message, User $user, Group $group)
    {
        $this->message = $message;
        $this->user = $user;
        $this->group = $group;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('group.'.$this->group->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new.group.message';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'time' => $this->message->created_at->format('h:i A'),
            'date' => $this->message->created_at->format('M d, Y'),
            'sender' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'initials' => $this->user->initials(),
            ],
            'group' => [
                'id' => $this->group->id,
                'name' => $this->group->name,
            ],
        ];
    }
}
