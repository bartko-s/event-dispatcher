<?php

declare(strict_types=1);

namespace Stefano\EventDispatcher\Logger;

class EventLog
{
    public function __construct(
        private readonly float $time,
        private readonly string $eventClass,
        private readonly ?string $eventName = null,
        private readonly ?string $eventInfo = null
    ) {
    }

    public function getTime(): float
    {
        return $this->time;
    }

    public function getEventClass(): string
    {
        return $this->eventClass;
    }

    public function getEventInfo(): ?string
    {
        return $this->eventInfo;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }
}
