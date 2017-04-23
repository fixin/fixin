<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Cargo;

class Cargo extends CargoBase
{
    protected const
        THIS_SETS = parent::THIS_SETS + [
            self::CONTENT_TYPE => self::USING_SETTER
        ];

    /**
     * @var string
     */
    protected $contentType = '';

    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return $this
     */
    public function setContentType(string $contentType): CargoInterface
    {
        $this->contentType = $contentType;

        return $this;
    }
}
