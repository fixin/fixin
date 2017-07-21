<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Validator;

use Fixin\Resource\Prototype;

abstract class AbstractValidator extends Prototype implements ValidatorInterface
{
    /**
     * @var string[]
     */
    protected $errorTemplates = [];

    /**
     * @var string[]
     */
    protected $errors = [];

    public function __invoke($value, $context = null): bool
    {
        return $this->isValid($value, $context);
    }

    protected function addError(string $error): void
    {
        $this->errors[$error] = $this->errorTemplates[$error] ?? $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function isValid($value, $context = null): bool
    {
        $this->errors = [];

        return true;
    }
}
