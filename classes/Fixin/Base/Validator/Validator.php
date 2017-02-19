<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Validator;

use Fixin\Resource\Resource;

abstract class Validator extends Resource implements ValidatorInterface
{
    /**
     * @var string[]
     */
    protected $errorTemplates = [];

    /**
     * @var string[]
     */
    protected $errors = [];

    public function __invoke($value): bool
    {
        return $this->isValid($value);
    }

    /**
     * @return static
     */
    protected function addError(string $error): Validator
    {
        $this->errors[$error] = $this->errorTemplates[$error] ?? $error;

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function isValid($value): bool
    {
        $this->errors = [];

        return true;
    }
}
