<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\Base\Stream\Stream;
use Fixin\ResourceManager\ResourceManagerInterface;
use Fixin\View\ViewInterface;

class PhpEngine extends Engine {

    const NO_TEMPLATE = 'No template';

    /**
     * @var AssistantInterface
     */
    protected $assistant;

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     * @param string $name
     */
    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null) {
        parent::__construct($container, $options, $name);

        $this->assistant = $this->container->clonePrototype('View\Engine\Assistant')->withEngine($this);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Engine\EngineInterface::render($view)
     */
    public function render(ViewInterface $view) {
        // Template
        $filename = $view->getResolvedTemplate();
        if (is_null($filename)) {
            return static::NO_TEMPLATE;
        }

        // Data
        $data = $this->renderChildren($view) + $view->getVariables();

        // Include
        try {
            ob_start();
            EncapsulatedInclude::include(clone $this->assistant, $filename, $data);
        }
        catch (\Throwable $t) {
            ob_end_clean();

            throw $t;
        }

        return ob_get_clean();
    }
}