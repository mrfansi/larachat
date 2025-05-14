<div class="h-full flex flex-col">
    <div class="p-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-700">Groups</h2>
            <button 
                wire:click="toggleCreateGroupModal"
                class="p-2 rounded-full bg-blue-500 text-white hover:bg-blue-600 focus:outline-none"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>
        <div class="mt-2">
            <div class="relative">
                <input 
                    type="text" 
                    placeholder="Search groups..." 
                    class="w-full px-4 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500"
                />
                <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
            </div>
        </div>
    </div>
    
    <div class="flex-1 overflow-y-auto p-2">
        @if($groups->isEmpty())
            <div class="p-4 text-center text-gray-500">
                <p>You're not in any groups yet.</p>
                <button 
                    wire:click="toggleCreateGroupModal"
                    class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none"
                >
                    Create a group
                </button>
            </div>
        @else
            <ul>
                @foreach($groups as $group)
                    <li 
                        wire:key="group-{{ $group->id }}"
                        wire:click="selectGroup({{ $group->id }})"
                        class="p-3 rounded-lg mb-2 hover:bg-gray-200 cursor-pointer {{ $selectedGroupId === $group->id ? 'bg-gray-200' : '' }}"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center">
                                <span class="text-white font-medium">{{ substr($group->name, 0, 1) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $group->name }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    Created by {{ $group->creator->name }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <!-- Unread message indicator (can be implemented later) -->
                                <span class="hidden bg-purple-500 text-white text-xs rounded-full px-2 py-1">5</span>
                                
                                <!-- Member count -->
                                <span class="inline-flex items-center text-xs text-gray-500">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    {{ $group->users_count ?? $group->users->count() }}
                                </span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Handle group joined event from invitation link
        Livewire.on('group-joined', ({ groupId }) => {
            // Use Alpine to dispatch the show-groups event
            window.dispatchEvent(new CustomEvent('show-groups'));
            
            // Select the joined group
            @this.selectGroup(groupId);
        });
    });
</script>
