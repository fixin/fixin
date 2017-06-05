<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Resource\Prototype;
use Fixin\Support\Types;

abstract class AbstractCargo extends Prototype implements CargoInterface
{
    protected const
        THIS_SETS = [
            self::CONTENT => [self::USING_SETTER, Types::NULL],
            self::DELIVERED => self::USING_SETTER
        ];

    /**
     * @var mixed
     */
    protected $content;

    /**
     * @var bool
     */
    protected $delivered = false;

    public function getContent()
    {
        return $this->content;
    }

    public function isDelivered(): bool
    {
        return $this->delivered;
    }

    /**
     * @return $this
     */
    public function setContent($content): CargoInterface
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return $this
     */
    public function setDelivered(bool $delivered): CargoInterface
    {
        $this->delivered = $delivered;

        return $this;
    }

    /**
     * @return $this
     */
    public function unpack(): CargoInterface
    {
        echo $this->content;

        return $this;
    }
}
