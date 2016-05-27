<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Validator;

use Fixin\ResourceManager\ResourceInterface;

interface ValidatorInterface extends ResourceInterface {

    /**
     * Get errors of last validation
     *
     * @return array
     */
    public function getErrors(): array;

    /**
     * Validate value
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value): bool;
}