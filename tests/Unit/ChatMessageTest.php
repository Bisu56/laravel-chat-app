<?php

namespace Tests\Unit;

use App\Models\ChatMessage;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatMessageTest extends TestCase
{
    use RefreshDatabase;

test('chat message belongs to sender', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $message = ChatMessage::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Hello there!',
    ]);

    expect($message->sender)->toBeInstanceOf(User::class)
        ->and($message->sender->id)->toBe($sender->id);
});

test('chat message belongs to receiver', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $message = ChatMessage::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Hello there!',
    ]);

    expect($message->receiver)->toBeInstanceOf(User::class)
        ->and($message->receiver->id)->toBe($receiver->id);
});

test('chat message can be created with fillable attributes', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $message = ChatMessage::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Test message',
    ]);

    expect($message)->toBeInstanceOf(ChatMessage::class)
        ->and($message->message)->toBe('Test message')
        ->and($message->sender_id)->toBe($sender->id)
        ->and($message->receiver_id)->toBe($receiver->id);
});

test('chat message has timestamps', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $message = ChatMessage::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Test message',
    ]);

    expect($message->created_at)->not->toBeNull()
        ->and($message->updated_at)->not->toBeNull();
});
}
