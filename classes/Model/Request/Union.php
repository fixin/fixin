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
use Fixin\Support\Types;

class Union extends Prototype implements UnionInterface
{
    protected const
        THIS_SETS = [
            self::REQUEST => RequestInterface::class,
            self::TYPE => Types::STRING
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
