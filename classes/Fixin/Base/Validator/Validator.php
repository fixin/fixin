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

    protected function addError(string $error): void
    {
        $this->errors[$error] = $this->errorTemplates[$error] ?? $error;
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
