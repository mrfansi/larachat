<?php

namespace App\Livewire\Chat;

use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PrivateChat extends Component
{
    /**
     * The ID of the contact the user is chatting with.
     */
    public ?int $contactId = null;

    /**
     * The message being composed.
     */
    public string $message = '';

    /**
     * Whether the user is currently typing.
     */
    protected bool $isTyping = false;

    /**
     * Debounce timer for typing indicator.
     */
    protected $typingTimer;

    /**
     * Max characters for a message.
     */
    protected int $maxMessageLength = 1000;

    /**
     * Listen for the contact-selected event from ContactsList.
     */
    #[On('contact-selected')]
    public function loadConversation(int $contactId)
    {
        $this->contactId = $contactId;
        $this->message = '';

        // Mark all messages from this contact as read
        Message::where('sender_id', $this->contactId)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get the contact user instance.
     */
    public function getContactProperty()
    {
        if (! $this->contactId) {
            return null;
        }

        return User::find($this->contactId);
    }

    /**
     * Get messages between the current user and the selected contact.
     */
    public function getMessagesProperty(): Collection
    {
        if (! $this->contactId) {
            return collect();
        }

        return Message::where(function ($query) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $this->contactId);
        })
            ->orWhere(function ($query) {
                $query->where('sender_id', $this->contactId)
                    ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Send a message to the selected contact.
     */
    public function sendMessage()
    {
        if (empty(trim($this->message)) || ! $this->contactId) {
            return;
        }

        // Validate message
        $this->validate([
            'message' => 'required|string|max:'.$this->maxMessageLength,
        ]);

        // Create new message
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->contactId,
            'content' => $this->message,
        ]);

        // Broadcast the message
        broadcast(new MessageSent($message, Auth::user()))->toOthers();

        // Reset the message input and typing state
        $this->message = '';
        $this->isTyping = false;

        // Dispatch event to update the UI
        $this->dispatch('messageSent');
    }

    /**
     * Handle typing indicator.
     */
    public function updated($name, $value)
    {
        if ($name === 'message' && $this->contactId) {
            if (! empty($value) && ! $this->isTyping) {
                $this->isTyping = true;
                broadcast(new UserTyping(Auth::user(), $this->contactId))->toOthers();
            } elseif (empty($value) && $this->isTyping) {
                $this->isTyping = false;
            }
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.chat.private-chat', [
            'contact' => $this->contact,
            'messages' => $this->messages,
        ]);
    }
}
