<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Event;

use Fixin\Resource\PrototypeInterface;

interface EventManagerInterface extends PrototypeInterface
{
    /**
     * Add event
     *
     * @param string $name
     * @return callable
     */
    public function addEvent(string $name): callable;

    /**
     * Add listener
     *
     * @param string $name
     * @param callable $listener
     * @return $this
     */
    public function addListener(string $name, callable $listener): EventManagerInterface;

    /**
     * Remove event
     *
     * @param callable $callback
     * @return $this
     */
    public function removeEvent(callable $callback): EventManagerInterface;

    /**
     * Remove listener
     *
     * @param string $name
     * @param callable $listener
     * @return $this
     */
    public function removeListener(string $name, callable $listener): EventManagerInterface;

    /**
     * Remove listener from all
     *
     * @param callable $listener
     * @return $this
     */
    public function removeListenerFromAll(callable $listener): EventManagerInterface;
}
