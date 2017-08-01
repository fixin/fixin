<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Event;

use Fixin\Resource\Prototype;
use Fixin\Support\Types;

class Event extends Prototype implements EventInterface
{
    protected const
        THIS_SETS = [
            self::CONTEXT => [Types::ANY, Types::NULL],
            self::NAME => Types::STRING
        ];

    /**
     * @var mixed
     */
    protected $context;

    /**
     * @var string
     */
    protected $name;

    public function getContext()
    {
        return $this->context;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
