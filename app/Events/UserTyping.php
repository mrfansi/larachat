<?php

namespace App\Events;

use App\Models\Group;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user who is typing.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The recipient id or group id.
     *
     * @var int
     */
    public $recipientId;

    /**
     * Indicates if this is a group typing notification.
     *
     * @var bool
     */
    public $isGroup;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, int $recipientId, bool $isGroup = false)
    {
        $this->user = $user;
        $this->recipientId = $recipientId;
        $this->isGroup = $isGroup;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->isGroup) {
            return [
                new PresenceChannel('group.'.$this->recipientId),
            ];
        }

        return [
            new PrivateChannel('typing.'.$this->recipientId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'user.typing';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}
