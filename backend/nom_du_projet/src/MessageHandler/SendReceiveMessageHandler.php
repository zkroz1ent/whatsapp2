<?php

namespace App\MessageHandler;

use App\Message\SendReceiveMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendReceiveMessageHandler implements MessageHandlerInterface
{
    public function __invoke(SendReceiveMessage $message)
    {
        // do something with your message
    }
}
