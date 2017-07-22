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
    public function addEvent(string $name): callable;
    public function addListener(string $name, callable $listener): EventManagerInterface;
    public function removeEvent(callable $callback): EventManagerInterface;
    public function removeListener(string $name, callable $listener): EventManagerInterface;
    public function removeListenerFromAll(callable $listener): EventManagerInterface;
}
