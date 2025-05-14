<x-layouts.app>
    <div class="h-screen flex flex-col">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900">Chat</h1>
            </div>
        </header>
        
        <main class="flex-1 overflow-hidden">
            <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 h-full">
                <div class="flex h-full bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Sidebar with Contacts and Groups -->
                    <div class="w-72 bg-gray-100 border-r border-gray-200 flex flex-col">
                        <!-- Tabs for Contacts and Groups -->
                        <div class="flex border-b border-gray-200">
                            <button 
                                class="flex-1 py-4 px-2 text-center hover:bg-gray-200 focus:outline-none"
                                x-data="{}"
                                x-on:click="
                                    $dispatch('show-contacts');
                                    $el.classList.add('bg-gray-200', 'font-bold');
                                    $el.nextElementSibling.classList.remove('bg-gray-200', 'font-bold');
                                ">
                                Contacts
                            </button>
                            <button 
                                class="flex-1 py-4 px-2 text-center hover:bg-gray-200 focus:outline-none"
                                x-data="{}"
                                x-on:click="
                                    $dispatch('show-groups');
                                    $el.classList.add('bg-gray-200', 'font-bold');
                                    $el.previousElementSibling.classList.remove('bg-gray-200', 'font-bold');
                                ">
                                Groups
                            </button>
                        </div>
                        
                        <!-- Contacts List -->
                        <div 
                            x-data="{ show: true }"
                            x-on:show-contacts.window="show = true"
                            x-on:show-groups.window="show = false"
                            x-show="show"
                            class="flex-1 overflow-y-auto">
                            <livewire:chat.contacts-list />
                        </div>
                        
                        <!-- Groups List -->
                        <div 
                            x-data="{ show: false }"
                            x-on:show-contacts.window="show = false"
                            x-on:show-groups.window="show = true"
                            x-show="show"
                            class="flex-1 overflow-y-auto">
                            <livewire:chat.groups-list />
                        </div>
                    </div>
                    
                    <!-- Chat Area -->
                    <div class="flex-1 flex flex-col overflow-hidden">
                        <!-- Private Chat -->
                        <div 
                            x-data="{ show: true }"
                            x-on:show-contacts.window="show = true"
                            x-on:show-groups.window="show = false"
                            x-show="show"
                            class="flex-1 flex flex-col overflow-hidden">
                            <livewire:chat.private-chat />
                        </div>
                        
                        <!-- Group Chat -->
                        <div 
                            x-data="{ show: false }"
                            x-on:show-contacts.window="show = false"
                            x-on:show-groups.window="show = true"
                            x-show="show"
                            class="flex-1 flex flex-col overflow-hidden">
                            <livewire:chat.group-chat />
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Create Group Modal -->
    <livewire:chat.create-group />
    
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            // Initialize the Contacts tab as active
            document.querySelector('button:first-of-type').classList.add('bg-gray-200', 'font-bold');
            
            // Initialize Echo listeners
            window.addEventListener('livewire:initialized', () => {
                if (window.Echo) {
                    // Listen for private messages
                    Echo.private(`chat.${window.userId}.${window.receiverId}`)
                        .listen('.new.message', (e) => {
                            Livewire.dispatch('new-message-received', { senderId: e.sender.id });
                        });
                    
                    // Listen for typing indicators
                    Echo.private(`typing.${window.userId}`)
                        .listen('.user.typing', (e) => {
                            // This will be handled by the UI components
                        });
                        
                    // You'll need to join presence channels for groups when a group is selected
                }
            });
            
            // Handle clipboard copy for invitation links
            document.addEventListener('copy-to-clipboard', (e) => {
                const text = e.detail.text;
                if (text) {
                    navigator.clipboard.writeText(text).then(() => {
                        // Success notification can be handled here
                    }).catch(err => {
                        console.error('Could not copy text: ', err);
                    });
                }
            });
        });
    </script>
    @endpush
</x-layouts.app>
