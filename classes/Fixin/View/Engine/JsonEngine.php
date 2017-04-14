<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Engine;

use Fixin\Base\Json\Json;
use Fixin\Base\Json\JsonInterface;
use Fixin\View\ViewInterface;

class JsonEngine extends Engine
{
    protected const
        CONTENT_TYPE = 'application/json';

    /**
     * @var Json
     */
    protected $json;

    public function render(ViewInterface $view): string
    {
        $json = $this->json ?? ($this->json = $this->resourceManager->get('Base\Json\Json', JsonInterface::class));
        $result = [];

        // Children
        foreach ($this->renderChildren($view) as $key => $rendered) {
            $result[] = "{$json->encode($key)}:{$rendered}";
        }

        // Variables
        foreach ($view->getVariables() as $key => $value) {
            $result[] = $json->encode($key) . ':' . $json->encode($value);
        }

        return '{' . implode(',', $result) . '}';
    }
}
