<?php

declare(strict_types=1);

namespace Stefano\EventDispatcher;

use Contributte\EventDispatcher\LazyEventDispatcher;
use Nette\DI\Container;
use Stefano\EventDispatcher\Logger\EventLog;
use Stefano\EventDispatcher\Logger\ListenerLog;
use Stefano\EventDispatcher\Logger\Logger;

class LazyEventLoggerDispatcher extends LazyEventDispatcher
{
    private Logger $logger;

    public function __construct(Container $container, Logger $logger)
    {
        parent::__construct($container);
        $this->logger = $logger;
    }

    public function dispatch(object $event, string $eventName = null): object
    {
        $eventInfo = method_exists($event, '__toString') ? (string) $event : null;

        $start = microtime(true) * 1000;
        $r = parent::dispatch($event, $eventName);
        $log = new EventLog((microtime(true) * 1000) - $start, $event::class, $eventName, $eventInfo);
        $this->logger->logEvent($log);

        return $r;
    }

    protected function callListeners(iterable $listeners, string $eventName, object $event)
    {
		$stoppable = $event instanceof StoppableEventInterface;
		foreach ($listeners as $listener) {
			$wasStopped = $stoppable && $event->isPropagationStopped();
			$start = microtime(true) * 1000;
            parent::callListeners(array($listener), $eventName, $event);
            $log = new ListenerLog(
                (microtime(true) * 1000) - $start,
                $this->createListenerName($listener),
                $eventName,
                method_exists($event, '__toString') ? (string) $event : '',
				$wasStopped
            );
            $this->logger->logListener($log);
        }
    }

    private function createListenerName($listener): string
    {
		switch (true) {
			case is_string($listener) && strpos($listener, '::'):
				return '[static] ' . $listener;
			case is_string($listener):
				return '[function] ' . $listener;
			case is_array($listener) && is_object($listener[0]):
				return '[method] ' . get_class($listener[0])  . '->' . $listener[1];
			case is_array($listener):
				return '[static] ' . $listener[0]  . '::' . $listener[1];
			case $listener instanceof Closure:
				return '[closure]';
			case is_object($listener):
				return '[invokable] ' . get_class($listener);
			default:
				return 'Cannot listener convert to string';
		}
    }
}
