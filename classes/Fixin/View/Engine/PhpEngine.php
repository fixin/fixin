<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

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
     * @see \Fixin\View\Engine\EngineInterface::render()
     */
    public function render(ViewInterface $view) {
        return $this->renderInner($view);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Engine\Engine::renderInner()
     */
    protected function renderInner(ViewInterface $view) {
        // Template
        $filename = $view->getResolvedTemplate();
        if (is_null($filename)) {
            return static::NO_TEMPLATE;
        }

        // Include
        try {
            ob_start();
            EncapsulatedInclude::include(clone $this->assistant, $filename, $this->fetchData($view));
        }
        catch (\Throwable $t) {
            ob_end_clean();

            throw $t;
        }

        return ob_get_clean();
    }
}