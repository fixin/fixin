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

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function isDelivered(): bool
    {
        return $this->delivered;
    }

    /**
     * @inheritDoc
     */
    public function setContent($content): CargoInterface
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDelivered(bool $delivered): CargoInterface
    {
        $this->delivered = $delivered;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unpack(): CargoInterface
    {
        echo $this->content;

        return $this;
    }
}
