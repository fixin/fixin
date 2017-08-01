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

interface EventInterface extends PrototypeInterface
{
    public const
        CONTEXT = 'context',
        NAME = 'name';

    public function getContext();
    public function getName(): string;
}
