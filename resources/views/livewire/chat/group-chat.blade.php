<div class="h-full flex flex-col">
    @if(!$groupId)
        <!-- No group selected state -->
        <div class="h-full flex items-center justify-center">
            <div class="text-center p-6">
                <div class="flex justify-center mb-4">
                    <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-700">No group selected</h2>
                <p class="text-gray-500 mt-1">Select a group to start chatting</p>
            </div>
        </div>
    @else
        <!-- Group Chat header -->
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <!-- Group avatar -->
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center">
                        <span class="text-white font-medium">{{ substr($group->name, 0, 1) }}</span>
                    </div>
                    
                    <!-- Group info -->
                    <div class="ml-3">
                        <div class="text-gray-900 font-semibold">{{ $group->name }}</div>
                        <div class="text-xs text-gray-500">
                            {{ $members->count() }} members
                        </div>
                    </div>
                </div>
                
                <!-- Group actions dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button 
                        @click="open = !open" 
                        class="p-2 rounded-full hover:bg-gray-200 focus:outline-none"
                    >
                        <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </button>
                    
                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10"
                    >
                        <div class="py-1">
                            <button 
                                wire:click="copyInvitationLink"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none"
                            >
                                Copy invitation link
                            </button>
                            <!-- More actions can be added here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Messages container -->
        <div 
            class="flex-1 p-4 overflow-y-auto"
            x-data="{}"
            x-init="$nextTick(() => { $el.scrollTop = $el.scrollHeight; })"
            x-on:groupMessageSent.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight; })"
        >
            <!-- Date separator -->
            <div class="text-center text-xs text-gray-500 mb-4">Today</div>
            
            @forelse($messages as $msg)
                <div 
                    wire:key="group-msg-{{ $msg->id }}"
                    class="mb-4 {{ $msg->sender_id === auth()->id() ? 'text-right' : 'text-left' }}"
                >
                    @if($msg->sender_id !== auth()->id())
                        <div class="flex items-start mb-1">
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center">
                                <span class="text-white text-xs font-medium">{{ $msg->sender->initials() }}</span>
                            </div>
                            <span class="ml-2 text-xs font-medium text-gray-900">{{ $msg->sender->name }}</span>
                        </div>
                    @endif
                    
                    <div 
                        class="inline-block rounded-lg px-4 py-2 max-w-xs lg:max-w-md {{ $msg->sender_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}"
                    >
                        {{ $msg->content }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ $msg->created_at->format('h:i A') }}
                    </div>
                </div>
            @empty
                <div class="h-full flex items-center justify-center">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="mt-2 text-gray-500">No messages in this group yet.</p>
                        <p class="text-sm text-gray-400">Start the conversation by sending a message!</p>
                    </div>
                </div>
            @endforelse
            
            <!-- User joined notifications can be displayed here -->
        </div>
        
        <!-- Message input -->
        <div class="border-t border-gray-200 p-4">
            <form wire:submit="sendMessage">
                <div class="flex items-center">
                    <div class="flex-1">
                        <input 
                            type="text" 
                            wire:model.live="message" 
                            placeholder="Type a message..." 
                            class="w-full px-4 py-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500"
                            x-data="{}"
                            x-on:keydown.enter="!$event.shiftKey && $event.preventDefault()"
                        />
                    </div>
                    <div class="ml-3">
                        <button 
                            type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg focus:outline-none"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif
</div>

<script>
    // Setup Echo listeners for presence channel when group changes
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('group-selected', ({ groupId }) => {
            if (window.Echo) {
                Echo.join(`group.${groupId}`)
                    .here((users) => {
                        console.log('Users currently in the group:', users);
                        // Update UI to show online users
                    })
                    .joining((user) => {
                        console.log('User joined:', user);
                        // Display a notification that a user joined
                    })
                    .leaving((user) => {
                        console.log('User left:', user);
                        // Update UI to show user went offline
                    })
                    .listen('.new.group.message', (e) => {
                        // Handled by the Livewire component
                        console.log('New group message:', e);
                    })
                    .listen('.user.typing', (e) => {
                        // Show typing indicator
                        console.log('User typing:', e);
                    })
                    .listen('.user.joined', (e) => {
                        console.log('User joined group:', e);
                        // Display notification that a new user joined the group
                    });
            }
        });
        
        // Handle invitation link copied notification
        Livewire.on('invitation-link-copied', () => {
            // Show notification that link was copied
            console.log('Invitation link copied to clipboard');
            // You can add a toast notification here
        });
    });
</script>
