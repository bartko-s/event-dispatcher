<?php

declare(strict_types=1);

namespace Stefano\EventDispatcher\Tracy;

use Contributte\EventDispatcheruse\Logger\Logger;
use Nette\Bridges\ApplicationLatte\Template;
use Tracy\Debugger;

class EventTracyPanelTemplate extends Template
{
    public Logger $logger;
    public Debugger $debugger;
}
