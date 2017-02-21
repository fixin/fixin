<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

use Fixin\Base\Escaper\EscaperInterface;
use Fixin\Resource\ResourceManagerInterface;

class HtmlAttributes extends Helper
{
    /**
     * @var EscaperInterface
     */
    protected $escaper;

    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null)
    {
        parent::__construct($container, $options, $name);

        $this->escaper = $container->get('Base\Escaper\Escaper');
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
