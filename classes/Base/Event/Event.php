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

    /**
     * @inheritDoc
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }
}
