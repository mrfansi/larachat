<?php

namespace App\Livewire\Chat;

use App\Events\UserJoinedGroup;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class CreateGroup extends Component
{
    /**
     * The group name.
     */
    public string $name = '';

    /**
     * Indicates if we're showing the modal.
     */
    public bool $showModal = false;

    /**
     * Form validation rules.
     *
     * @return array<string, string>
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:50',
        ];
    }

    /**
     * Create a new group.
     */
    public function createGroup()
    {
        $this->validate();

        // Generate a unique invitation code
        $invitationCode = Str::random(10);

        // Create the group
        $group = Group::create([
            'name' => $this->name,
            'invitation_code' => $invitationCode,
            'created_by' => Auth::id(),
        ]);

        // Add the creator to the group
        $group->users()->attach(Auth::id(), ['joined_at' => now()]);

        // Broadcast that the user joined the group
        broadcast(new UserJoinedGroup(Auth::user(), $group));

        // Reset the form
        $this->reset('name');

        // Notify the parent component that a group was created
        $this->dispatch('group-created', groupId: $group->id);

        // Close the modal
        $this->showModal = false;
    }

    /**
     * Show the create group modal.
     */
    public function show()
    {
        $this->showModal = true;
    }

    /**
     * Hide the create group modal.
     */
    public function hide()
    {
        $this->showModal = false;
        $this->reset('name');
        $this->resetValidation();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.chat.create-group');
    }
}
