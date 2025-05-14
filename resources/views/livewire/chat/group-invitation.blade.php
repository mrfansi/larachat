<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Group Invitation
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Join a group chat by entering the invitation code
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            @if($hasError)
                <div class="rounded-md bg-red-50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Error
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>{{ $errorMessage }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($group)
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-purple-100 mb-4">
                        <span class="text-3xl font-medium text-purple-700">{{ substr($group->name, 0, 1) }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $group->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Created by {{ $group->creator->name }} &bull; {{ $group->users->count() }} members
                    </p>
                </div>
                
                <div>
                    <button 
                        wire:click="joinGroup"
                        type="button"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Join Group Chat
                    </button>
                </div>
            @else
                <form wire:submit="joinGroup">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">
                            Invitation Code
                        </label>
                        <div class="mt-1">
                            <input 
                                wire:model="code" 
                                type="text" 
                                name="code" 
                                id="code" 
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter the invitation code"
                                required
                            />
                        </div>
                        @error('code') 
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <button 
                            type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Join Group
                        </button>
                    </div>
                </form>
            @endif
            
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            Or
                        </span>
                    </div>
                </div>

                <div class="mt-6">
                    <a 
                        href="{{ route('chat') }}"
                        class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Go to Chat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
