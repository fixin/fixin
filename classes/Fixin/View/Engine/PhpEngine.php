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
        $data = $this->fetchData($view);

        // Template
        $filename = $view->getResolvedTemplate();
        if (is_null($filename)) {
            return $data;
        }

        // Clone assistant
        $assistant = clone $this->assistant;

        ob_start();

        // Include
        try {
            fixinViewEngineEncapsulatedInclude($assistant, $filename, $data);
        }
        catch (\Throwable $t) {
            ob_end_clean();

            throw $t;
        }

        return ob_get_clean();
    }
}