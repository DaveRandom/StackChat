<?php declare(strict_types = 1);

namespace Room11\StackExchangeChatClient\Client\Actions;

use Amp\Artax\Request;
use Room11\StackExchangeChatClient\Client\PostedMessageTracker;
use Room11\StackExchangeChatClient\Message\Command;
use Room11\StackExchangeChatClient\Room\Room as ChatRoom;
use Psr\Log\LoggerInterface as Logger;

class ActionFactory
{
    private $logger;
    private $tracker;

    public function __construct(Logger $logger, PostedMessageTracker $tracker)
    {
        $this->logger = $logger;
        $this->tracker = $tracker;
    }

    public function createPostMessageAction(Request $request, ChatRoom $room, string $text, ?Command $originatingCommand): PostMessageAction
    {
        return new PostMessageAction($this->logger, $request, $room, $this->tracker, $text, $originatingCommand);
    }

    public function createEditMessageAction(Request $request, ChatRoom $room): EditMessageAction
    {
        return new EditMessageAction($this->logger, $request, $room);
    }

    public function createMoveMessageAction(Request $request, ChatRoom $room): MoveMessageAction
    {
        return new MoveMessageAction($this->logger, $request, $room);
    }

    public function createPinOrUnpinMessageAction(Request $request, ChatRoom $room): PinOrUnpinMessageAction
    {
        return new PinOrUnpinMessageAction($this->logger, $request, $room);
    }

    public function createUnstarMessageAction(Request $request, ChatRoom $room): UnstarMessageAction
    {
        return new UnstarMessageAction($this->logger, $request, $room);
    }
}
