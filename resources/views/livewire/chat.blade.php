<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Messages') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Chat with your contacts') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex h-[600px] rounded-2xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden bg-white dark:bg-zinc-900">
        <!-- Left: User List -->
        <div class="w-full md:w-80 border-e border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 @if(!$showUserList) hidden @endif md:block flex flex-col">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                <div class="relative">
                    <flux:icon name="magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" />
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        class="w-full pl-10 pr-4 py-2 text-sm bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder:text-zinc-400"
                        placeholder="Search conversations..."
                    >
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                @forelse($users as $user)
                    <div 
                        wire:click="selectUser({{ $user->id }})" 
                        class="p-3 cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700/50 transition-all duration-200 border-b border-zinc-100 dark:border-zinc-700/50"
                        :class="{'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-l-blue-500': {{ $selectedUser && $selectedUser->id === $user->id ? 'true' : 'false' }}}"
                    >
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium text-sm">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white dark:border-zinc-800"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $user->name }}</div>
                                <div class="text-xs text-zinc-500 truncate">{{ $user->email }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-zinc-500 text-sm">No conversations yet</div>
                @endforelse
            </div>
        </div>

        <!-- Right: Chat Section -->
        <div class="w-full md:flex-1 flex flex-col @if($showUserList) hidden @endif md:flex bg-white dark:bg-zinc-900">
            @if($selectedUser)
                <!-- Header -->
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <button wire:click="showUsers" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 md:hidden p-1">
                            <flux:icon name="arrow-left" class="w-5 h-5" />
                        </button>
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium text-sm">
                                    {{ strtoupper(substr($selectedUser->name, 0, 2)) }}
                                </div>
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white dark:border-zinc-900"></div>
                            </div>
                            <div>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $selectedUser->name }}</div>
                                <div class="text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    Online
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-1 p-4 overflow-y-auto space-y-4 bg-zinc-50/50 dark:bg-zinc-800/30" id="chat-messages">
                    @forelse($messages as $message)
                        <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            @if($message->sender_id !== auth()->id())
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-xs font-medium mr-2 shrink-0">
                                    {{ strtoupper(substr($selectedUser->name, 0, 2)) }}
                                </div>
                            @endif
                            <div class="max-w-[70%]">
                                <div class="px-4 py-2.5 rounded-2xl shadow-sm text-sm leading-relaxed
                                    {{ $message->sender_id === auth()->id() 
                                        ? 'bg-blue-600 text-white rounded-br-md' 
                                        : 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-bl-md border border-zinc-200 dark:border-zinc-700' }}">
                                    {{ $message->message }}
                                </div>
                                <div class="text-[10px] text-zinc-400 mt-1 {{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                                    {{ $message->created_at->format('g:i A') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex items-center justify-center">
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                    <flux:icon name="chat-bubble-left-right" class="w-8 h-8 text-zinc-400" />
                                </div>
                                <p class="text-zinc-500">No messages yet</p>
                                <p class="text-zinc-400 text-sm">Send a message to start the conversation!</p>
                            </div>
                        </div>
                    @endforelse

                    @if($isTyping)
                        <div class="flex justify-start">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-xs font-medium mr-2 shrink-0">
                                {{ strtoupper(substr($selectedUser->name, 0, 2)) }}
                            </div>
                            <div class="bg-white dark:bg-zinc-800 rounded-2xl rounded-bl-md px-4 py-3 shadow-sm border border-zinc-200 dark:border-zinc-700">
                                <div class="flex gap-1">
                                    <div class="w-2 h-2 bg-zinc-400 rounded-full animate-pulse"></div>
                                    <div class="w-2 h-2 bg-zinc-400 rounded-full animate-pulse" style="animation-delay: 0.15s"></div>
                                    <div class="w-2 h-2 bg-zinc-400 rounded-full animate-pulse" style="animation-delay: 0.3s"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Input -->
                <form wire:submit="submit" class="p-4 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900">
                    <div class="flex items-center gap-2 bg-zinc-100 dark:bg-zinc-800 rounded-full px-2 py-2">
                        <button type="button" class="p-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 rounded-full hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                            <flux:icon name="face-smile" class="w-5 h-5" />
                        </button>
                        <input 
                            wire:model="newMessage"
                            wire:keydown="typing"
                            type="text"
                            class="flex-1 bg-transparent border-0 focus:outline-none focus:ring-0 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400"
                            placeholder="Type a message..."
                        >
                        <button 
                            type="submit"
                            class="p-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            @if(empty($newMessage)) disabled @endif
                        >
                            <flux:icon name="paper-airplane" class="w-4 h-4" />
                        </button>
                    </div>
                </form>
            @else
                <div class="flex-1 flex items-center justify-center bg-zinc-50 dark:bg-zinc-800/30">
                    <div class="text-center p-8">
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            <flux:icon name="chat-bubble-oval-left-ellipsis" class="w-12 h-12 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Your Messages</h3>
                        <p class="text-zinc-500 max-w-xs">Select a conversation from the sidebar to start chatting</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@script
<script>
    Livewire.on('message-sent', () => {
        const chatBox = document.getElementById('chat-messages');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });

    Livewire.on('scroll-to-bottom', () => {
        const chatBox = document.getElementById('chat-messages');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });

    Livewire.on('reset-typing-indicator', (timeout) => {
        setTimeout(() => {
            Livewire.dispatch('resetTypingIndicator');
        }, timeout);
    });
</script>
@endscript
