<div>
   <div class="relative mb-6 w-full">
    <flux:heading size="xl" level="1">{{ __('Chat') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage your profile and account settings') }}</flux:subheading>
    <flux:separator variant="subtle" />
</div>

<div class="flex h-[550px] text-sm border rounded-xl shadow overflow-hidden bg-white md:flex-row flex-col">

    <!-- Left: User List -->

    <div class="w-full md:w-1/4 border-r bg-gray-50 @if(!$showUserList) hidden @endif md:block">

        <div class="p-4 font-bold text-gray-700 border-b">Users</div>

        <div class="p-4">

            <div class="relative">

                <span class="absolute inset-y-0 left-0 flex items-center pl-3">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">

                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />

                    </svg>

                </span>

                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring focus:ring-blue-300" placeholder="Search users...">

            </div>

        </div>

                <div class="divide-y">

                    @forelse($users as $user)

                    <div wire:click="selectUser({{ $user->id }})" class="p-3 cursor-pointer hover:bg-blue-100 transition"

                        :class="{'bg-blue-200': {{ $selectedUser && $selectedUser->id === $user->id ? 'true' : 'false' }}}">

                        <div class="text-gray-800">{{ $user->name }}</div>

                        <div class="text-xs text-gray-500">{{ $user->email }}</div>

                    </div>

                    @empty

                    <div class="p-3 text-center text-gray-500">No users found.</div>

                    @endforelse

                </div>

            </div>



        <!-- Right: Chat Section -->



        <div class="w-full md:w-3/4 flex flex-col @if($showUserList) hidden @endif md:flex">



            @if($selectedUser)



            <!-- Header -->



            <div class="p-4 border-b bg-gray-50 flex items-center justify-between">



                <div class="flex items-center gap-4">



                    <button wire:click="showUsers" class="text-gray-600 hover:text-gray-800 md:hidden">



                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">



                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />



                        </svg>



                    </button>



                    <div>



                        <div class="text-lg font-semibold text-gray-800">{{ $selectedUser->name }}</div>



                        <div class="text-xs text-gray-500">{{ $selectedUser->email }}</div>



                    </div>



                </div>



            </div>



                    <!-- Messages -->



                    <div class="flex-1 p-4 overflow-y-auto space-y-2 bg-gray-50">



                        @forelse($messages as $message)



                            



                        <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">



                            <div class="max-w-xs px-4 py-2 rounded-2xl shadow



                                {{ $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' }}">



                                {{ $message->message }}



                            </div>



                        </div>



                        @empty



                        <div class="text-center text-gray-500">No messages yet. Start the conversation!</div>



                        @endforelse



                        @if($isTyping)



                        <div class="flex justify-start">



                            <div class="max-w-xs px-4 py-2 rounded-2xl shadow bg-gray-200 text-gray-800">



                                Typing...



                            </div>



                        </div>



                        @endif



                    </div>



    



    



                    <!-- Input -->



    



    



                    <form wire:submit="submit" class="p-4 border-t bg-white flex items-center gap-2">



    



    



                        <input 



    



    



                            wire:model="newMessage"



    



    



                            wire:keydown="typing"



    



    



                            type="text"



    



    



                            class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-300"



    



    



                            placeholder="Type your message..." />



    



    



                        <button type="submit"



    



    



                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-full transition">



    



    



                            Send



    



    



                        </button>



    



    



                    </form>



            @else



            <div class="flex-1 flex items-center justify-center text-gray-500">



                Select a user to start chatting.



            </div>



            @endif



        </div>



    </div>



    </div>

@script

<script>

    Livewire.on('message-sent', () => {

        const chatBox = document.querySelector('.overflow-y-auto');

        chatBox.scrollTop = chatBox.scrollHeight;

    });

    Livewire.on('scroll-to-bottom', () => {

        const chatBox = document.querySelector('.overflow-y-auto');

        chatBox.scrollTop = chatBox.scrollHeight;

    });

    Livewire.on('reset-typing-indicator', (timeout) => {

        setTimeout(() => {

            @this.call('resetTypingIndicator');

        }, timeout);

    });

</script>

@endscript





