<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\Resource\Resource;
use Fixin\View\Helper\HelperInterface;
use Fixin\View\ViewInterface;

abstract class Engine extends Resource implements EngineInterface
{
    protected const
        CONTENT_TYPE = 'text/html',
        EXCEPTION_INVALID_HELPER_NAME = "Invalid helper name: '%s'",
        EXCEPTION_NAME_COLLISION = "Child-variable name collision: '%s'",
        HELPER_NAME_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';

    /**
     * @var HelperInterface[]
     */
    protected $helpers = [];

    public function getContentType(): string
    {
        return static::CONTENT_TYPE;
    }

    public function getHelper(string $name): HelperInterface
    {
        return $this->helpers[$name] ?? ($this->helpers[$name] = $this->produceHelper($name));
    }

    /**
     * Produce helper instance
     *
     * @throws Exception\InvalidArgumentException
     */
    protected function produceHelper(string $name): HelperInterface
    {
        if (preg_match(static::HELPER_NAME_PATTERN, $name)) {
            return $this->container->clone('View\Helper\\' . ucfirst($name), [
                HelperInterface::OPTION_ENGINE => $this
            ]);
        }

        throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_HELPER_NAME, $name));
    }

    /**
     * @throws Exception\KeyCollisionException
     */
    protected function renderChildren(ViewInterface $view): array
    {
        $data = [];
        $dataByObject = new \SplObjectStorage();

        foreach ($view->getChildren() as $name => $child) {
            $data[$name] = $dataByObject[$child] ?? ($dataByObject[$child] = $child->render());
        }

        // Test name collision
        $variables = $view->getVariables();

        if ($names = array_intersect_key($data, $variables)) {
            throw new Exception\KeyCollisionException(sprintf(static::EXCEPTION_NAME_COLLISION, implode("', '", array_keys($names))));
        }

        return $data;
    }
}
