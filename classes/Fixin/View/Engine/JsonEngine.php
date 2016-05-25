<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\Base\Json\Json;
use Fixin\ResourceManager\ResourceManagerInterface;
use Fixin\View\ViewInterface;

class JsonEngine extends Engine {

    /**
     * @var Json
     */
    protected $json;

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     * @param string $name
     */
    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null) {
        parent::__construct($container, $options, $name);

        $this->json = $this->container->get('Base\Json\Json');
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Engine\EngineInterface::render($view)
     */
    public function render(ViewInterface $view) {
        $json = $this->json;
        $result = [];

        // Children
        foreach ($this->renderChildren($view) as $key => $value) {
            $result[] = $json->encode($key) . ':' . $value;
        }

        // Variables
        foreach ($view->getVariables() as $key => $value) {
            $result[] = $json->encode($key) . ':' . $json->encode($value);
        }

        return '{' . implode(',', $result) . '}';
    }
}