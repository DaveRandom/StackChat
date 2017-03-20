<?php declare(strict_types = 1);

namespace Room11\StackChat\Room;

use const Room11\StackChat\ROOM_IDENTIFIER_EXPR;

class Room
{
    private $id;
    private $host;

    public static function createFromIdentString(string $ident): Room
    {
        if (!preg_match('/^' . ROOM_IDENTIFIER_EXPR . '$/i', $ident, $matches)) {
            throw new \InvalidArgumentException('Invalid ident string: ' . $ident);
        }

        return new self((int)$matches[2], $matches[1]);
    }

    public function __construct(int $id, string $host)
    {
        $this->id = $id;
        $this->host = strtolower($host);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getIdentString(): string
    {
        return $this->host . '#' . $this->id;
    }

    public function equals(Room $room): bool
    {
        return $this->host === $room->getHost() && $this->id === $room->getId();
    }

    public function __toString()
    {
        return $this->getIdentString();
    }
}
