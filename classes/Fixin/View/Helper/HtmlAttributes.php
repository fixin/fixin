<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

use Fixin\ResourceManager\ResourceManagerInterface;

class HtmlAttributes extends Helper {

    /**
     * @var EscaperInterface
     */
    protected $escaper;

    /**
     * @param ResourceManagerInterface $container
     * @param array $options
     * @param string $name
     */
    public function __construct(ResourceManagerInterface $container, array $options = null, string $name = null) {
        parent::__construct($container, $options, $name);

        $this->escaper = $container->get('Base\Escaper\Escaper');
    }

    /**
     * Escape
     *
     * @param array $var
     * @return \Fixin\View\Helper\string
     */
    public function __invoke(array $var) {
        return $this->escape($var);
    }

    /**
     * Escape array
     *
     * @param array $var
     * @return string
     */
    public function escape(array $var): string {
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
     *
     * @param mixed $var
     * @return string
     */
    protected function escapeValue($var): string {
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