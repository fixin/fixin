<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request;

use Fixin\Resource\Prototype;

class Union extends Prototype implements UnionInterface
{
    protected const
        THIS_REQUIRES = [
            self::REQUEST,
            self::TYPE
        ],
        THIS_SETS = [
            self::REQUEST => RequestInterface::class,
            self::TYPE => self::STRING_TYPE
        ];

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $type;

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
