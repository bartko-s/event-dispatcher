<?php

declare(strict_types=1);

namespace Stefano\EventDispatcher\Logger;

class ListenerLog
{
    public function __construct(
        private readonly float $time,
        private readonly string $listener,
        private readonly string $eventClass,
        private readonly string $eventInfo,
        private readonly bool $wasStopped,
    ) {
    }

    public function getTime(): float
    {
        return $this->time;
    }

    public function getListener(): string
    {
        return $this->listener;
    }

    public function getEventClass(): string
    {
        return $this->eventClass;
    }

    public function getEventInfo(): string
    {
        return $this->eventInfo;
    }

    public function wasStopped(): bool
    {
        return $this->wasStopped;
    }
}
