<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Engine;

use Fixin\Resource\ResourceManagerInterface;
use Fixin\View\ViewInterface;

class PhpEngine extends Engine
{
    protected const
        NO_TEMPLATE = 'No template';

    /**
     * @var AssistantInterface
     */
    protected $assistant;

    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null)
    {
        parent::__construct($container, $options, $name);

        $this->assistant = $this->container->get('View\Engine\Assistant')->withEngine($this);
    }

    public function render(ViewInterface $view): string
    {
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
