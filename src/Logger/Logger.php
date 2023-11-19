<?php

declare(strict_types=1);

namespace Stefano\EventDispatcher\Logger;

class Logger
{
    /** @var EventLog[] */
    private array $eventLogs = array();

    /** @var ListenerLog[] */
    private array $listenerLogs = array();

    public function logEvent(EventLog $log): void
    {
        $this->eventLogs[] = $log;
    }

    public function logListener(ListenerLog $log): void
    {
        $this->listenerLogs[] = $log;
    }

    /**
     * @return EventLog[]
     */
    public function getEventLogs(): array
    {
        return $this->eventLogs;
    }

    /**
     * @return ListenerLog[]
     */
    public function getListenerLogs(): array
    {
        return $this->listenerLogs;
    }

    public function getTotalMs(): float
    {
        $totalTime = 0;
        foreach ($this->getEventLogs() as $log) {
            $totalTime += $log->getTime();
        }

        return $totalTime;
    }
}
