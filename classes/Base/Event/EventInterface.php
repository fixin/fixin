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

    /**
     * Get context
     *
     * @return mixed
     */
    public function getContext();

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string;
}
