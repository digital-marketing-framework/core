<?php

namespace DigitalMarketingFramework\Core\Model\Queue;

use DateTime;
use DigitalMarketingFramework\Core\Model\ItemInterface;

class Error implements ItemInterface
{
    /**
     * @param array<string,int> $types
     */
    public function __construct(
        protected string $message,
        protected int $count,
        protected JobInterface $lastSeen,
        protected DateTime $lastSeenTime,
        protected JobInterface $firstSeen,
        protected DateTime $firstSeenTime,
        protected array $types,
    ) {
    }

    /**
     * @param array{message:string,count:int,lastSeen:JobInterface,lastSeenTime:DateTime,firstSeen:JobInterface,firstSeenTime:DateTime,types:array<string,int>} $record
     */
    public static function fromDataRecord(array $record): Error
    {
        return new Error(
            $record['message'],
            $record['count'],
            $record['lastSeen'],
            $record['lastSeenTime'],
            $record['firstSeen'],
            $record['firstSeenTime'],
            $record['types'],
        );
    }

    public function getId(): null
    {
        return null;
    }

    public function setId(int|string|null $id): void
    {
    }

    public function getLabel(): string
    {
        return $this->getMessage();
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getLastSeen(): JobInterface
    {
        return $this->lastSeen;
    }

    public function getLastSeenTime(): DateTime
    {
        return $this->lastSeenTime;
    }

    public function getFirstSeen(): JobInterface
    {
        return $this->firstSeen;
    }

    public function getFirstSeenTime(): DateTime
    {
        return $this->firstSeenTime;
    }

    /**
     * @return array<string,int>
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}
