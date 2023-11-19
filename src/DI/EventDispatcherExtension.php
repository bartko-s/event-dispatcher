<?php declare(strict_types = 1);

namespace Stefano\EventDispatcher\DI;

use Contributte\EventDispatcher\DI\EventDispatcherExtension as ContributteEventDispatcherExtension;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\ServiceCreationException;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Stefano\EventDispatcher\LazyEventLoggerDispatcher;
use Stefano\EventDispatcher\Logger\Logger;
use Stefano\EventDispatcher\Tracy\EventTracyPanel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tracy\Debugger;

/**
 * @property-read stdClass $config
 */
class EventDispatcherExtension extends ContributteEventDispatcherExtension
{

    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'lazy' => Expect::bool(true),
            'autoload' => Expect::bool(true),
            'tracyLogger' => Expect::bool(true),
        ]);
    }

    /**
     * Register services
     */
    public function loadConfiguration(): void
    {
        if(!$this->isTracyLogger()) {
            parent::loadConfiguration();
            return;
        }

        if(!$this->config->lazy) {
            // logger is not implemented
            parent::loadConfiguration();
        } else {
            $builder = $this->getContainerBuilder();

            $eventDispatcherDefinition = $builder->addDefinition($this->prefix('dispatcher'))
                ->setType(EventDispatcherInterface::class);

            $this->getContainerBuilder()->addDefinition($this->prefix('logger'))
                ->setFactory(Logger::class);

            $this->getContainerBuilder()->addDefinition($this->prefix('eventTracyPanel'))
                ->setFactory(EventTracyPanel::class);

            $eventDispatcherDefinition
                ->setFactory(LazyEventLoggerDispatcher::class);
        }
    }

    public function afterCompile(ClassType $class): void
    {
        if($this->isTracyLogger()) {
            $initialize = $class->getMethod('initialize');
            $initialize->addBody('$this->getService(?)->addPanel($this->getService(?));', array('tracy.bar', $this->prefix('eventTracyPanel')));
        }
    }

    private function isTracyLogger(): bool
    {
        if(!class_exists('\Tracy\Debugger')) {
            return false;
        }

        if (Debugger::$productionMode) {
            return false;
        }

        return $this->config->tracyLogger;
    }
}
