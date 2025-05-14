<?php

namespace App\Livewire\Chat;

use App\Models\Group;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class GroupsList extends Component
{
    /**
     * The selected group ID.
     */
    public ?int $selectedGroupId = null;

    /**
     * The show create group modal flag.
     */
    public bool $showCreateGroupModal = false;

    /**
     * Initialization method that runs once when the component is mounted.
     */
    public function mount()
    {
        // Initialize with the first group selected if any groups exist
        $firstGroup = $this->groups->first();
        if ($firstGroup) {
            $this->selectedGroupId = $firstGroup->id;
        }
    }

    /**
     * Get all groups for the current user.
     */
    #[Computed]
    public function groups(): Collection
    {
        return Auth::user()->groups()->with('creator')->get();
    }

    /**
     * Select a group for conversation.
     */
    public function selectGroup(int $groupId)
    {
        $this->selectedGroupId = $groupId;

        // Dispatch an event to load the group conversation
        $this->dispatch('group-selected', groupId: $groupId);
    }

    /**
     * Toggle the create group modal.
     */
    public function toggleCreateGroupModal()
    {
        $this->showCreateGroupModal = ! $this->showCreateGroupModal;
    }

    /**
     * Handle when a new group is created.
     */
    #[On('group-created')]
    public function handleGroupCreated(int $groupId)
    {
        $this->selectGroup($groupId);
        $this->showCreateGroupModal = false;
    }

    /**
     * Mark a group as having a new unread message.
     * This can be triggered from the Echo listener when a new message arrives.
     */
    #[On('new-group-message-received')]
    public function markGroupWithNewMessage(int $groupId)
    {
        // This will be implemented with UI updates when we design the view
        // For now we're just setting up the event listener

        // If the group receiving the message is the currently selected one,
        // we don't need to indicate a new message
        if ($this->selectedGroupId == $groupId) {
            return;
        }

        // Refresh the groups list to show new message indicators
        $this->dispatch('$refresh');
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.chat.groups-list', [
            'groups' => $this->groups,
        ]);
    }
}
