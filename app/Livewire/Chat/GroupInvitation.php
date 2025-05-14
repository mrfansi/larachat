<?php

namespace App\Livewire\Chat;

use App\Events\UserJoinedGroup;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Component;

class GroupInvitation extends Component
{
    /**
     * The invitation code.
     */
    #[Rule('required|string|size:10')]
    public string $code = '';

    /**
     * Indicates if there's an error.
     */
    public bool $hasError = false;

    /**
     * Error message.
     */
    public string $errorMessage = '';

    /**
     * Initialize the component with the invitation code if provided.
     */
    public function mount($code = null)
    {
        if ($code) {
            $this->code = $code;
            $this->joinGroup();
        }
    }

    /**
     * Get the group associated with the invitation code.
     */
    #[Computed]
    public function group()
    {
        if (empty($this->code)) {
            return null;
        }

        return Group::where('invitation_code', $this->code)->first();
    }

    /**
     * Join the group using the invitation code.
     */
    public function joinGroup()
    {
        $this->validate();

        $this->hasError = false;
        $this->errorMessage = '';

        // Find the group
        $group = $this->group;

        if (! $group) {
            $this->hasError = true;
            $this->errorMessage = 'Invalid invitation code. The group was not found.';

            return;
        }

        // Check if the user is already a member
        if (Auth::user()->groups->contains($group->id)) {
            $this->hasError = true;
            $this->errorMessage = 'You are already a member of this group.';

            return;
        }

        // Add the user to the group
        $group->users()->attach(Auth::id(), ['joined_at' => now()]);

        // Broadcast that the user joined
        broadcast(new UserJoinedGroup(Auth::user(), $group));

        // Redirect to the chat page and select this group
        $this->redirect(route('chat'), navigate: true);

        // Dispatch an event to select this group in the groups list
        $this->dispatch('group-joined', groupId: $group->id);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.chat.group-invitation', [
            'group' => $this->group,
        ]);
    }
}
