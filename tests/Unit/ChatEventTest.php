<?php

namespace Tests\Unit;

use App\Events\MessageSent;
use App\Events\UserTyping;
use Tests\TestCase;

class ChatEventTest extends TestCase
{
    public function test_message_sent_event_can_be_created(): void
    {
        $message = [
            'id' => 1,
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'Hello!',
        ];

        $event = new MessageSent($message);

        $this->assertEquals($message, $event->message);
    }

    public function test_message_sent_event_broadcasts_on_private_channel(): void
    {
        $message = [
            'id' => 1,
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'Hello!',
        ];

        $event = new MessageSent($message);
        $channels = $event->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertEquals('private-chat.2', $channels[0]->name);
    }

    public function test_message_sent_event_returns_correct_broadcast_data(): void
    {
        $message = [
            'id' => 1,
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'Hello!',
        ];

        $event = new MessageSent($message);
        $broadcastData = $event->broadcastWith();

        $this->assertArrayHasKey('id', $broadcastData);
        $this->assertArrayHasKey('sender_id', $broadcastData);
        $this->assertArrayHasKey('receiver_id', $broadcastData);
        $this->assertArrayHasKey('message', $broadcastData);
        $this->assertEquals('Hello!', $broadcastData['message']);
    }

    public function test_user_typing_event_can_be_created(): void
    {
        $event = new UserTyping(1, 2);

        $this->assertEquals(1, $event->userId);
        $this->assertEquals(2, $event->recipientId);
    }

    public function test_user_typing_event_broadcasts_on_private_channel(): void
    {
        $event = new UserTyping(1, 2);
        $channel = $event->broadcastOn();

        $this->assertEquals('private-typing.1', $channel->name);
    }

    public function test_message_sent_event_implements_should_broadcast(): void
    {
        $message = [
            'id' => 1,
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'Hello!',
        ];

        $event = new MessageSent($message);

        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, $event);
    }

    public function test_user_typing_event_implements_should_broadcast(): void
    {
        $event = new UserTyping(1, 2);

        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, $event);
    }
}
