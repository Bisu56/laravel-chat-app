<?php

namespace Tests\Unit;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_message_belongs_to_sender(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $message = ChatMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Hello there!',
        ]);

        $this->assertInstanceOf(User::class, $message->sender);
        $this->assertEquals($sender->id, $message->sender->id);
    }

    public function test_chat_message_belongs_to_receiver(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $message = ChatMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Hello there!',
        ]);

        $this->assertInstanceOf(User::class, $message->receiver);
        $this->assertEquals($receiver->id, $message->receiver->id);
    }

    public function test_chat_message_can_be_created_with_fillable_attributes(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $message = ChatMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Test message',
        ]);

        $this->assertInstanceOf(ChatMessage::class, $message);
        $this->assertEquals('Test message', $message->message);
        $this->assertEquals($sender->id, $message->sender_id);
        $this->assertEquals($receiver->id, $message->receiver_id);
    }

    public function test_chat_message_has_timestamps(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $message = ChatMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Test message',
        ]);

        $this->assertNotNull($message->created_at);
        $this->assertNotNull($message->updated_at);
    }
}
