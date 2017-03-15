<?php declare(strict_types=1);

namespace Room11\StackExchangeChatClient\Message;

use Amp\Promise;
use Amp\Success;
use Room11\StackExchangeChatClient\Event\MessageEvent;
use Room11\StackExchangeChatClient\Storage\CommandAlias as CommandAliasStorage;
use function Amp\resolve;

class Factory
{
    private const INVOKER = '!!'; // todo: make this configurable

    private $aliasMap;

    public function __construct(CommandAliasStorage $aliasStorage)
    {
        $this->aliasMap = $aliasStorage;
    }

    private function splitCommandText(string $commandText, string $invoker)
    {
        preg_match('#^' . preg_quote($invoker, '#') . '(\S+)(.*)#u', $commandText, $match);

        return [strtolower($match[1]), $match[2] ?? ''];
    }

    private function buildCommand(MessageEvent $event)
    {
        $commandText = $event->getMessageContent()->textContent;

        list($commandName, $parameterString) = $this->splitCommandText($commandText, self::INVOKER);

        while (null !== $alias = yield $this->aliasMap->get($event->getRoom(), $commandName)) {
            $replaceExpr = '#(?<=^' . preg_quote(self::INVOKER, '#') . ')' . preg_quote($commandName, '#') . '#u';
            $commandText = preg_replace($replaceExpr, $alias, $commandText);

            list($commandName, $parameterString) = $this->splitCommandText($commandText, self::INVOKER);
        }

        $parameters = preg_split('#\s+#', $parameterString, -1, PREG_SPLIT_NO_EMPTY);

        return new Command($event, $event->getRoom(), $commandName, $parameters);
    }

    private function isCommandMessage(MessageEvent $event)
    {
        $messageElement = $event->getMessageContent()->documentElement;

        return $messageElement instanceof \DOMElement
            && strtolower($messageElement->tagName) === 'p'
            && $messageElement->firstChild instanceof \DOMText
            && strpos($messageElement->textContent, self::INVOKER) === 0;
    }

    public function build(MessageEvent $event): Promise
    {
        return $this->isCommandMessage($event)
            ? resolve($this->buildCommand($event))
            : new Success(new Message($event, $event->getRoom()));
    }
}
