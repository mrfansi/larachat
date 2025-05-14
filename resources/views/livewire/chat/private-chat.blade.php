<div class="h-full flex flex-col">
    @if(!$contactId)
        <!-- No contact selected state -->
        <div class="h-full flex items-center justify-center">
            <div class="text-center p-6">
                <div class="flex justify-center mb-4">
                    <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-700">No conversation selected</h2>
                <p class="text-gray-500 mt-1">Choose a contact to start chatting</p>
            </div>
        </div>
    @else
        <!-- Chat header -->
        <div class="border-b border-gray-200 px-4 py-3">
            <div class="flex items-center">
                <!-- Contact avatar -->
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                    <span class="text-white font-medium">{{ $contact->initials() }}</span>
                </div>
                
                <!-- Contact info -->
                <div class="ml-3">
                    <div class="text-gray-900 font-semibold">{{ $contact->name }}</div>
                    
                    <!-- Typing indicator (shown when the other user is typing) -->
                    <div class="text-xs text-gray-500 typing-indicator hidden">
                        typing...
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Messages container -->
        <div 
            class="flex-1 p-4 overflow-y-auto"
            x-data="{}"
            x-init="$nextTick(() => { $el.scrollTop = $el.scrollHeight; })"
            x-on:messageSent.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight; })"
        >
            <!-- Date separator -->
            <div class="text-center text-xs text-gray-500 mb-4">Today</div>
            
            @forelse($messages as $msg)
                <div 
                    wire:key="msg-{{ $msg->id }}"
                    class="mb-4 {{ $msg->sender_id === auth()->id() ? 'text-right' : 'text-left' }}"
                >
                    <div 
                        class="inline-block rounded-lg px-4 py-2 max-w-xs lg:max-w-md {{ $msg->sender_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}"
                    >
                        {{ $msg->content }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ $msg->created_at->format('h:i A') }}
                        
                        <!-- Read indicator (only for sent messages) -->
                        @if($msg->sender_id === auth()->id())
                            <span class="{{ $msg->read_at ? 'text-blue-500' : 'text-gray-400' }} ml-1">
                                <svg class="inline-block h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="h-full flex items-center justify-center">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="mt-2 text-gray-500">No messages yet.</p>
                        <p class="text-sm text-gray-400">Start the conversation by sending a message!</p>
                    </div>
                </div>
            @endforelse
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
    // Setup Echo listeners for current chat channel when contact changes
    document.addEventListener('livewire:initialized', () => {
        // Set up typing indicator
        Livewire.on('contact-selected', ({ contactId }) => {
            if (window.Echo) {
                Echo.private(`typing.${contactId}`)
                    .listen('.user.typing', (e) => {
                        // Show typing indicator
                        const indicator = document.querySelector('.typing-indicator');
                        if (indicator) {
                            indicator.classList.remove('hidden');
                            
                            // Hide after 3 seconds if no new typing events
                            setTimeout(() => {
                                indicator.classList.add('hidden');
                            }, 3000);
                        }
                    });
            }
        });
    });
</script>
