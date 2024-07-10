<?php

namespace App\Message;


final class SendReceiveMessage
{
    private $content;
    private $senderId;
    private $receiverId;

    public function __construct(string $content, int $senderId, int $receiverId)
    {
        $this->content = $content;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSenderId(): int
    {
        return $this->senderId;
    }

    public function getReceiverId(): int
    {
        return $this->receiverId;
    }
}
