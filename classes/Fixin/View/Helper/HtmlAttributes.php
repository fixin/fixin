<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\View\Helper;

class HtmlAttributes extends EscapeHelper {

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Helper\EscapeHelper::__invoke($value)
     */
    public function __invoke($value) {
        return $this->escape($value);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\View\Helper\EscapeHelper::escape($value)
     */
    public function escape($value): string {
        $html = [];
        $escaper = $this->escaper;

        foreach ($value as $key => $item) {
            if (is_null($value)) {
                continue;
            }

            if (is_array($item)) {
                if (empty($item)) {
                    $html[] = $escaper->escapeHtml($key) . '="' . $this->escapeArray($item) . '"';
                }

                continue;
            }

            $html[] = $escaper->escapeHtml($key) . '="' . $escaper->escapeHtml($item) . '"';
        }

        return implode(' ', $html);
    }

    /**
     * Escape array item like "width: 80px; height: 20em"
     *
     * @param array $value
     * @return string
     */
    protected function escapeArray(array $value): string {
        $escaper = $this->escaper;

        $list = [];
        foreach ($value as $subkey => $subvalue) {
            $list[] = $escaper->escapeHtml($subkey) . ': ' . $escaper->escapeHtml($subvalue);
        }

        return implode('; ', $list);
    }
}