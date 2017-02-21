<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Request;

use Fixin\Resource\Prototype;

class Union extends Prototype implements UnionInterface
{
    protected const
        THIS_REQUIRES = [
            self::OPTION_REQUEST => self::TYPE_INSTANCE,
            self::OPTION_TYPE => self::TYPE_STRING
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

    protected function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }

    protected function setType(string $type)
    {
        $this->type = $type;
    }
}
