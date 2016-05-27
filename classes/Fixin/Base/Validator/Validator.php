<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Validator;

abstract class Validator implements ValidatorInterface {

    /**
     * @var array
     */
    protected $errorTemplates = [];

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * Add error
     *
     * @param string $error
     * @return \Fixin\Base\Validator\Validator
     */
    protected function addError(string $error) {
        $this->errors[$error] = $this->errorTemplates[$error] ?? $error;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Validator\ValidatorInterface::getErrors()
     */
    public function getErrors(): array {
        return $this->errors;
    }
}