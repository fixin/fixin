<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\View\Helper;

use Fixin\Base\Escaper\EscaperInterface;
use Fixin\Resource\ResourceManagerInterface;

class HtmlAttributes extends AbstractHelper
{
    /**
     * @var EscaperInterface
     */
    protected $escaper;

    public function __construct(ResourceManagerInterface $resourceManager, array $options = null, string $name = null)
    {
        parent::__construct($resourceManager, $options, $name);

        $this->escaper = $resourceManager->get('*\Base\Escaper\Escaper', EscaperInterface::class);
    }

    /**
     * Invoke escape
     */
    public function __invoke(array $var): string
    {
        return $this->escape($var);
    }

    /**
     * Escape array
     */
    public function escape(array $var): string
    {
        $escaper = $this->escaper;

        $html = [];

        foreach ($var as $key => $value) {
            if ('' !== $result = $this->escapeValue($value)) {
                $html[] = "{$escaper->escapeHtml($key)}=\"$result\"";
            }
        }

        return implode(' ', $html);
    }

    /**
     * Escape single value
     */
    protected function escapeValue($var): string
    {
        if (is_null($var)) {
            return '';
        }

        $escaper = $this->escaper;

        if (is_array($var)) {
            $list = [];
            foreach ($var as $key => $value) {
                $list[] = $escaper->escapeHtml($key) . ': ' . $escaper->escapeHtml($value);
            }

            return implode('; ', $list);
        }

        return $escaper->escapeHtml($var);
    }
}
