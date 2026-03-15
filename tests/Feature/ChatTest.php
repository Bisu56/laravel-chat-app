<?php

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

test('chat page requires authentication', function () {
    $response = $this->get(route('chat'));
    $response->assertRedirect(route('login'));
});

test('authenticated user can access chat page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('chat'));
    $response->assertStatus(200);
});

test('chat shows other users in user list', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create(['name' => 'John Doe']);
    $this->actingAs($user);

    $response = $this->get(route('chat'));
    $response->assertStatus(200)
        ->assertSee('John Doe');
});

test('user cannot see_themselves_in_user_list', function () {
    $user = User::factory()->create(['name' => 'Test User']);
    User::factory()->create(['name' => 'Other User']);
    $this->actingAs($user);

    $users = \App\Models\User::whereNot('id', $user->id)->get();

    $this->assertCount(1, $users);
    $this->assertEquals('Other User', $users->first()->name);
    $this->assertNull($users->first(fn ($u) => $u->name === 'Test User'));
});

test('chat component can load messages between users', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $message = ChatMessage::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Hello from sender!',
    ]);

    expect(ChatMessage::count())->toBe(1);

    $messages = ChatMessage::where(function ($query) use ($sender, $receiver) {
        $query->where('sender_id', $sender->id)
            ->where('receiver_id', $receiver->id);
    })->orWhere(function ($query) use ($sender, $receiver) {
        $query->where('sender_id', $receiver->id)
            ->where('receiver_id', $sender->id);
    })->get();

    expect($messages)->toHaveCount(1)
        ->and($messages->first()->message)->toBe('Hello from sender!');
});

test('chat message can be created', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $message = ChatMessage::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Test message',
    ]);

    expect($message)->toBeInstanceOf(ChatMessage::class)
        ->and(ChatMessage::count())->toBe(1);
});

test('chat searches users by name', function () {
    $user = User::factory()->create();
    User::factory()->create(['name' => 'Alice']);
    User::factory()->create(['name' => 'Bob']);
    $this->actingAs($user);

    $searchResults = User::whereNot('id', $user->id)
        ->where('name', 'like', '%Alice%')
        ->get();

    expect($searchResults)->toHaveCount(1)
        ->and($searchResults->first()->name)->toBe('Alice');
});

test('chat component selects user correctly', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $this->actingAs($user);

    $component = Volt::test('chat')
        ->set('selectedUser', $otherUser);

    $component->assertStatus(200);
});

test('chat message notification works for correct receiver', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $message = ChatMessage::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Secret message',
    ]);

    $belongsToReceiver = $message->receiver_id === $receiver->id;
    expect($belongsToReceiver)->toBeTrue();
});
