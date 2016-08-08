<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository\Where;

use Fixin\Model\Repository\RepositoryRequestInterface;

class WhereRequest extends Where {

    const OPTION_REQUEST = 'request';
    const THIS_REQUIRES = [
        self::OPTION_REQUEST => self::TYPE_INSTANCE,
    ];

    /**
     * @var RepositoryRequestInterface
     */
    protected $request;

    /**
     * Get request
     *
     * @return RepositoryRequestInterface
     */
    public function getRequest(): RepositoryRequestInterface {
        return $this->request;
    }

    /**
     * Set request
     *
     * @param RepositoryRequestInterface $request
     */
    protected function setRequest(RepositoryRequestInterface $request) {
        $this->request = $request;
    }
}
