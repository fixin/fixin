<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Validator;

use Fixin\Resource\ResourceInterface;

interface ValidatorInterface extends ResourceInterface
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
    public function isValid($value): bool;
}
