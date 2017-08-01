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
     */
    public function __invoke($value): bool;

    /**
     * Get errors of last validation
     */
    public function getErrors(): array;

    /**
     * Validate value
     */
    public function isValid($value, $context = null): bool;
}
