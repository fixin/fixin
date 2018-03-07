<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo;

class Cargo extends AbstractCargo
{
    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::CONTENT_TYPE => self::USING_SETTER
        ];

    /**
     * @var string
     */
    protected $contentType = '';

    /**
     * @inheritDoc
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @inheritDoc
     */
    public function setContentType(string $contentType): CargoInterface
    {
        $this->contentType = $contentType;

        return $this;
    }
}
