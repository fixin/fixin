<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Engine;

use Fixin\Resource\ResourceManagerInterface;
use Fixin\View\ViewInterface;
use Throwable;

class PhpEngine extends Engine
{
    protected const
        NO_TEMPLATE = 'No template';

    /**
     * @var AssistantInterface
     */
    protected $assistant;

    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        parent::__construct($resourceManager, $options, $name);

        $this->assistant = $this->resourceManager->clone('View\Engine\Assistant', AssistantInterface::class)->withEngine($this);
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
        catch (Throwable $t) {
            ob_end_clean();

            throw $t;
        }

        return ob_get_clean();
    }
}
