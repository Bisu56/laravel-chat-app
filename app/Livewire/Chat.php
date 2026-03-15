<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class Chat extends Component
{
    public $users;

    #[Url]
    public $selectedUser;

    public $newMessage;

    public $messages;

    public $loginID;

    public $search;

    public $showUserList = true;

    public $isTyping = false;

    public function mount()
    {
        $this->messages = new Collection;
        // Load users or any other initial data here
        $this->users = \App\Models\User::whereNot('id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->get();

        if ($this->selectedUser) {
            $this->selectUser($this->selectedUser);
        }

        $this->loginID = Auth::id();
        $this->dispatch('scroll-to-bottom');

    }

    public function selectUser($id)
    {
        $this->selectedUser = \App\Models\User::find($id);
        $this->loadMessages();
        $this->showUserList = false;
    }

    public function showUsers()
    {
        $this->showUserList = true;
    }

    public function loadMessages()
    {
        if (! $this->selectedUser) {
            return;
        }

        $this->messages = ChatMessage::where(function ($query) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $this->selectedUser->id);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->selectedUser->id)
                ->where('receiver_id', Auth::id());
        })->get();
    }

    public function submit()
    {
        if (! $this->newMessage) {
            return;
        }

        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'message' => $this->newMessage,
        ]);
        $this->messages->push($message);
        $this->newMessage = '';
        broadcast(new MessageSent($message->toArray()));
        $this->dispatch('message-sent', $message->toArray());

    }

    public function typing()
    {
        broadcast(new \App\Events\UserTyping($this->selectedUser->id, Auth::id()));
    }

    public function getListeners(): array
    {
        $listeners = [];

        $listeners['echo-private:chat.'.Auth::id().',App\Events\MessageSent'] = 'newChatMessageNotification';

        if ($this->selectedUser) {
            $listeners['echo-private:typing.'.$this->selectedUser->id.',App\Events\UserTyping'] = 'showTypingIndicator';
        }

        return $listeners;
    }

    public function newChatMessageNotification($message)
    {
        if ($this->selectedUser && $message['sender_id'] == $this->selectedUser->id) {
            $newMessage = ChatMessage::find($message['id']);
            if ($newMessage) {
                $this->messages->push($newMessage);
                $this->dispatch('scroll-to-bottom');
            }
        }
    }

    public function showTypingIndicator($event)
    {
        if ($this->selectedUser && $event['userId'] === $this->selectedUser->id) {
            $this->isTyping = true;
            $this->dispatch('scroll-to-bottom');
            $this->dispatch('reset-typing-indicator', 3000);
        }
    }

    public function resetTypingIndicator()
    {
        $this->isTyping = false;
    }

    public function render()
    {
        $this->users = \App\Models\User::whereNot('id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->get();

        return view('livewire.chat');
    }
}
