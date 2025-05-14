<?php

namespace App\Livewire\Chat;

use App\Events\GroupMessageSent;
use App\Events\UserTyping;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class GroupChat extends Component
{
    /**
     * The ID of the group the user is chatting in.
     */
    public ?int $groupId = null;

    /**
     * The message being composed.
     */
    public string $message = '';

    /**
     * Whether the user is currently typing.
     */
    protected bool $isTyping = false;

    /**
     * Max characters for a message.
     */
    protected int $maxMessageLength = 1000;

    /**
     * Listen for the group-selected event from GroupsList.
     */
    #[On('group-selected')]
    public function loadGroup(int $groupId)
    {
        $this->groupId = $groupId;
        $this->message = '';
    }

    /**
     * Get the group instance.
     */
    public function getGroupProperty()
    {
        if (! $this->groupId) {
            return null;
        }

        return Group::with('users')->find($this->groupId);
    }

    /**
     * Get members of the current group.
     */
    public function getMembersProperty(): Collection
    {
        if (! $this->group) {
            return collect();
        }

        return $this->group->users;
    }

    /**
     * Get messages for the current group.
     */
    public function getMessagesProperty(): Collection
    {
        if (! $this->groupId) {
            return collect();
        }

        return Message::with('sender')
            ->where('group_id', $this->groupId)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Get the group invitation link.
     */
    public function getInvitationLinkProperty(): ?string
    {
        if (! $this->group) {
            return null;
        }

        return route('groups.invitation', ['code' => $this->group->invitation_code]);
    }

    /**
     * Send a message to the group.
     */
    public function sendMessage()
    {
        if (empty(trim($this->message)) || ! $this->groupId) {
            return;
        }

        // Validate message
        $this->validate([
            'message' => 'required|string|max:'.$this->maxMessageLength,
        ]);

        // Create new message
        $message = Message::create([
            'sender_id' => Auth::id(),
            'group_id' => $this->groupId,
            'content' => $this->message,
        ]);

        // Broadcast the message
        broadcast(new GroupMessageSent($message, Auth::user(), $this->group))->toOthers();

        // Reset the message input and typing state
        $this->message = '';
        $this->isTyping = false;

        // Dispatch event to update the UI
        $this->dispatch('groupMessageSent');
    }

    /**
     * Handle typing indicator.
     */
    public function updated($name, $value)
    {
        if ($name === 'message' && $this->groupId) {
            if (! empty($value) && ! $this->isTyping) {
                $this->isTyping = true;
                broadcast(new UserTyping(Auth::user(), $this->groupId, true))->toOthers();
            } elseif (empty($value) && $this->isTyping) {
                $this->isTyping = false;
            }
        }
    }

    /**
     * Copy invitation link to clipboard.
     */
    public function copyInvitationLink()
    {
        $this->dispatch('copy-to-clipboard', text: $this->invitationLink);
        $this->dispatch('invitation-link-copied');
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.chat.group-chat', [
            'group' => $this->group,
            'members' => $this->members,
            'messages' => $this->messages,
            'invitationLink' => $this->invitationLink,
        ]);
    }
}
