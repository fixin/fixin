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
    const STREAM_MEMORY_SIZE = 256 * 1024;

    /**
     * @var AssistantInterface
     */
    protected $assistant;

    /**
     * @var Stream[]
     */
    protected $streamStack = [];

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
     * Writing to stream
     *
     * @param string $string
     * @return string
     */
    protected function streamHandler(string $string) {
        end($this->streamStack)->write($string);

        return '';
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

        // Stream
        $this->streamStack[] = new Stream('php://temp/maxmemory:' . static::STREAM_MEMORY_SIZE, 'wb+');

        // Include
        try {
            ob_start([$this, 'streamHandler'], static::STREAM_MEMORY_SIZE);
            EncapsulatedInclude::include(clone $this->assistant, $filename, $data);
        }
        catch (\Throwable $t) {
            ob_end_clean();
            array_pop($this->streamStack);

            throw $t;
        }

        ob_end_clean();

        return array_pop($this->streamStack);
    }
}