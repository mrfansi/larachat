<div class="h-full flex flex-col">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-700">Contacts</h2>
        <div class="mt-2">
            <div class="relative">
                <input 
                    type="text" 
                    placeholder="Search contacts..." 
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
        @if($contacts->isEmpty())
            <div class="p-4 text-center text-gray-500">
                <p>No contacts found.</p>
                <p class="text-sm mt-2">Start a conversation by sending a message to another user.</p>
            </div>
        @else
            <ul>
                @foreach($contacts as $contact)
                    <li 
                        wire:key="contact-{{ $contact->id }}"
                        wire:click="selectContact({{ $contact->id }})"
                        class="p-3 rounded-lg mb-2 hover:bg-gray-200 cursor-pointer {{ $selectedContactId === $contact->id ? 'bg-gray-200' : '' }}"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                <span class="text-white font-medium">{{ $contact->initials() }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $contact->name }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    Last message preview...
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <!-- Unread message indicator (can be implemented later) -->
                                <span class="hidden bg-blue-500 text-white text-xs rounded-full px-2 py-1">3</span>
                                
                                <!-- Online indicator (can be implemented later) -->
                                <span class="inline-block h-2 w-2 rounded-full bg-green-500"></span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
