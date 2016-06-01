<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Validator;

use Fixin\Resource\Resource;

abstract class Validator extends Resource implements ValidatorInterface {

    /**
     * @var string[]
     */
    protected $errorTemplates = [];

    /**
     * @var string[]
     */
    protected $errors = [];

    /**
     * Invoke isValid()
     *
     * @param mixed $value
     * @return bool
     */
    public function __invoke($value): bool {
        return $this->isValid($value);
    }

    /**
     * Add error
     *
     * @param string $error
     * @return self
     */
    protected function addError(string $error): Validator {
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