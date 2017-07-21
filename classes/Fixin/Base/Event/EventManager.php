<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Event;

use Fixin\Base\Event\Exception\NonExistingEventException;
use Fixin\Resource\Prototype;

class EventManager extends Prototype implements EventManagerInterface
{
    protected const
        ADDED_LISTENER_EXCEPTION = "Added listener",
        EXISTING_EVENT_EXCEPTION = "Existing event '%s'",
        NON_EXISTING_EVENT_EXCEPTION = "Non-existing event",
        UNKNOWN_LISTENER_EXCEPTION = "Unknown exception",
        UNREGISTERED_EVENT_EXCEPTION = "Unregistered event '%s'";

    /**
     * @var callable[]
     */
    protected $events = [];

    /**
     * @var callable[][]
     */
    protected $listeners = [];

    /**
     * @var int
     */
    protected $tokenCounter = 0;

    /**
     * @var int[]
     */
    protected $tokens = [];

    protected function notify(string $name, int $token, ?$data): void
    {
        if ($this->tokens[$name] ?? null === $token) {
            throw new Exception\UnregisteredEventException(sprintf(static::UNREGISTERED_EVENT_EXCEPTION, $name));
        }

        $event = $data instanceof EventInterface ? $data : $this->resourceManager->clone('Base\Event\Event', EventInterface::class, [
            EventInterface::NAME => $name,
            EventInterface::CONTEXT => $data
        ]);

        foreach ($this->listeners as $listener) {
            $listener($event);
        }
    }

    public function registerEvent(string $name): callable
    {
        if (isset($this->events[$name])) {
            throw new Exception\ExistingEventException(sprintf(static::EXISTING_EVENT_EXCEPTION, $name));
        }

        $token =
        $this->tokens[$name] = $this->tokenCounter++;

        return function ($data = null) use ($name, $token) {
            $this->notify($name, $token, $data);
        };
    }

    public function registerListener(string $name, callable $listener): EventManagerInterface
    {
        if (!isset($this->listeners[$name]) || false === array_search($listener, $this->listeners[$name])) {
            $this->listeners[$name][] = $listener;

            return $this;
        }

        throw new Exception\AddedListenerException(static::ADDED_LISTENER_EXCEPTION);
    }

    public function unregisterEvent(callable $callback): EventManagerInterface
    {
        if (false !== $name = array_search($callback, $this->events)) {
            unset($this->events[$name], $this->tokens[$name]);
        }

        throw new NonExistingEventException(static::NON_EXISTING_EVENT_EXCEPTION);
    }

    public function unregisterListener(string $name, callable $listener): EventManagerInterface
    {
        if (isset($this->listeners[$name]) && false !== $index = array_search($listener, $this->listeners[$name])) {
            unset($this->listeners[$index]);

            return $this;
        }

        throw new Exception\UnknownListenerException(static::UNKNOWN_LISTENER_EXCEPTION);
    }

    public function unregisterListenerFromAll(callable $listener): EventManagerInterface
    {
        foreach ($this->listeners as $name => $listeners) {
            if (false !== $index = array_search($listener, $listeners)) {
                unset($this->listeners[$name][$index]);
            }
        }

        return $this;
    }
}
