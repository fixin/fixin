<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Validator;

use Fixin\Resource\PrototypeInterface;

interface ValidatorInterface extends PrototypeInterface
{
    /**
     * Invoke isValid()
     *
     * @param $value
     * @param array|null $context
     * @return bool
     */
    public function __invoke($value, $context = null): bool;

    /**
     * Get errors of last validation
     *
     * @return array
     */
    public function getErrors(): array;

    /**
     * Validate value
     *
     * @param $value
     * @param null $context
     * @return bool
     */
    public function isValid($value, $context = null): bool;
}
