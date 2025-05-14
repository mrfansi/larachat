<?php

namespace App\Livewire\Chat;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ContactsList extends Component
{
    /**
     * The selected contact ID.
     */
    public ?int $selectedContactId = null;

    /**
     * Initialization method that runs once when the component is mounted.
     */
    public function mount()
    {
        // Initialize with the first contact selected if any contacts exist
        $firstContact = $this->contacts->first();
        if ($firstContact) {
            $this->selectedContactId = $firstContact->id;
        }
    }

    /**
     * Get all contacts for the current user.
     */
    #[Computed]
    public function contacts(): Collection
    {
        return Auth::user()->contacts();
    }

    /**
     * Select a contact for conversation.
     */
    public function selectContact(int $contactId)
    {
        $this->selectedContactId = $contactId;

        // Dispatch an event to load the conversation with this contact
        $this->dispatch('contact-selected', contactId: $contactId);
    }

    /**
     * Mark a contact as having a new unread message.
     * This can be triggered from the Echo listener when a new message arrives.
     */
    #[On('new-message-received')]
    public function markContactWithNewMessage(int $senderId)
    {
        // This will be implemented with UI updates when we design the view
        // For now we're just setting up the event listener

        // If the contact sending the message is the currently selected one,
        // we don't need to indicate a new message
        if ($this->selectedContactId == $senderId) {
            return;
        }

        // Refresh the contacts list to show new message indicators
        $this->dispatch('$refresh');
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.chat.contacts-list', [
            'contacts' => $this->contacts,
        ]);
    }
}
