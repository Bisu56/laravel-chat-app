<?php

use App\Events\MessageSent;
use App\Events\UserTyping;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('message sent event can be created', function () {
    $message = [
        'id' => 1,
        'sender_id' => 1,
        'receiver_id' => 2,
        'message' => 'Hello!',
    ];

    $event = new MessageSent($message);

    expect($event->message)->toBe($message);
});

test('message sent event broadcasts on private channel', function () {
    $message = [
        'id' => 1,
        'sender_id' => 1,
        'receiver_id' => 2,
        'message' => 'Hello!',
    ];

    $event = new MessageSent($message);
    $channels = $event->broadcastOn();

    expect($channels)->toHaveCount(1)
        ->and($channels[0]->name)->toBe('chat.2');
});

test('message sent event returns correct broadcast data', function () {
    $message = [
        'id' => 1,
        'sender_id' => 1,
        'receiver_id' => 2,
        'message' => 'Hello!',
    ];

    $event = new MessageSent($message);
    $broadcastData = $event->broadcastWith();

    expect($broadcastData)->toHaveKeys(['id', 'sender_id', 'receiver_id', 'message'])
        ->and($broadcastData['message'])->toBe('Hello!');
});

test('user typing event can be created', function () {
    $event = new UserTyping(1, 2);

    expect($event->userId)->toBe(1)
        ->and($event->recipientId)->toBe(2);
});

test('user typing event broadcasts on private channel', function () {
    $event = new UserTyping(1, 2);
    $channel = $event->broadcastOn();

    expect($channel->name)->toBe('typing.1');
});

test('message sent event implements should broadcast', function () {
    $message = [
        'id' => 1,
        'sender_id' => 1,
        'receiver_id' => 2,
        'message' => 'Hello!',
    ];

    $event = new MessageSent($message);

    expect($event)->toBeInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class);
});

test('user typing event implements should broadcast', function () {
    $event = new UserTyping(1, 2);

    expect($event)->toBeInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class);
});
