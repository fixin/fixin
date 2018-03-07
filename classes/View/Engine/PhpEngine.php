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

class PhpEngine extends AbstractEngine
{
    /**
     * @var AssistantInterface
     */
    protected $assistant;

    /**
     * @inheritDoc
     */
    public function __construct(ResourceManagerInterface $resourceManager, array $options)
    {
        parent::__construct($resourceManager, $options);

        $this->assistant = $this->resourceManager->clone('*\View\Engine\Assistant', AssistantInterface::class)->withEngine($this);
    }

    /**
     * @inheritDoc
     */
    public function render(ViewInterface $view): string
    {
        // Template
        $filename = $view->getResolvedTemplate();

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
