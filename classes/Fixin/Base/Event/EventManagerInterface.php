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
    public function registerEvent(string $name): callable;
    public function registerListener(string $name, callable $listener): EventManagerInterface;
    public function unregisterEvent(callable $callback): EventManagerInterface;
    public function unregisterListener(string $name, callable $listener): EventManagerInterface;
}
