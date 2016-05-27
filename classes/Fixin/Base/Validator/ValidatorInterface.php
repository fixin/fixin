<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Validator;

interface ValidatorInterface {

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