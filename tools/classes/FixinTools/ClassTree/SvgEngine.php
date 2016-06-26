<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace FixinTools\ClassTree;

use Fixin\Support\Strings;

class SvgEngine {

    /**
     * @var float
     */
    protected $ellipseRatio;

    /**
     * @var float
     */
    protected $fontSize;

    /**
     * @var array
     */
    protected $groups;

    /**
     * @var float
     */
    protected $itemSize = 0.03;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @var int
     */
    protected $ratio;

    /**
     * @param Processor $processor
     * @param int $ratio
     * @param float $ellipseRatio
     */
    public function __construct(Processor $processor, int $ratio, float $ellipseRatio) {
        $this->processor = $processor;
        $this->ratio = $ratio;
        $this->ellipseRatio = $ellipseRatio;
        $this->fontSize = $ratio * 0.28 * $this->itemSize;
    }

    /**
     * @param string $text
     * @return array
     */
    function explodeRows(string $text): array {
        $words = explode(' ', $text);

        $rows = [];
        $buffer = array_shift($words);

        while ($words) {
            $word = array_shift($words);

            if (mb_strlen($buffer . $word) < 12) {
                $buffer .= ' ' . $word;
                continue;
            }

            $rows[] = $buffer;
            $buffer = $word;
        }

        if ($buffer) {
            $rows[] = $buffer;
        }

        return $rows;
    }

    /**
     * @param array $items
     * @param float $x
     * @param float $y
     * @param float $startAngle
     * @param float $endAngle
     */
    protected function placeItems(array $items, float $x, float $y, float $startAngle, float $endAngle) {
        $divs = count($items);
        $angleStep = ($endAngle - $startAngle) / $divs;
        $angle = $startAngle + $angleStep / 2;
        $itemR = max($this->itemSize * 2.5, $divs * ($this->itemSize * 2.4) / M_PI / 2 * 360 / ($endAngle - $startAngle));

        foreach ($items as $item) {
            $px = $x + cos($angle * M_PI / 180) * $itemR;
            $py = $y + sin($angle * M_PI / 180) * $itemR * $this->ellipseRatio;

            $item->px = $px * $this->ratio;
            $item->py = $py * $this->ratio;

            $this->items[$item->getName()] = $item;

            if ($children = $item->getChildren()) {
                $childrenStep = min(max($angleStep, 160 / count($children)), 120);
                $childrenStart = $angle - $childrenStep / 1.1;
                $childrenEnd = $angle + $childrenStep / 1.1;

                $this->placeItems($children, $px, $py, $childrenStart, $childrenEnd);
            }

            // Step
            $angle += $angleStep;
        }
    }

    /**
     * @param array $groups
     * @return string
     */
    public function render(array $groups): string {
        $itemGroups = $this->processor->getGroups();

        $this->items = [];
        foreach ($groups as $name => $data) {
            $this->placeItems($itemGroups[$name], $data['x'], $data['y'], 0, 360);
        }

        return $this->renderLines() . $this->renderItems();
    }

    /**
     * @return string
     */
    protected function renderItems(): string {
        $source = '';
        $itemR = $this->itemSize * $this->ratio;

        foreach ($this->items as $item) {
            $classes = ['Item'];

            if ($item->isInterface()) { $classes[] = 'Interface'; }
            if ($item->isClass()) {
                if ($item->isAbstract()) { $classes[] = 'Abstract'; }
                if ($item->isPrototype()) { $classes[] = 'Prototype'; }
                if ($item->isResource()) { $classes[] = 'Resource'; }
                if ($item->isFactory()) { $classes[] = 'Factory'; }
                if ($item->isException()) { $classes[] = 'Exception'; }
            }
            if ($item->isTrait()) { $classes[] = 'Trait'; }

            $source .= '<g class="' . implode(' ', $classes) . "\">\n";

            $px = $item->px;
            $py = $item->py;

            // Ellipse
            $source .= $this->tag('ellipse', [
                'cx' => $px,
                'cy' => $py,
                'rx' => $itemR,
                'ry' => $itemR * $this->ellipseRatio,
            ]);

            // Text
            $tspans = '';
            $rows = $this->explodeRows(Strings::textFromCamelCase($item->getShortName()));
            $dy = -count($rows) * 0.7 + 0.25 + 0.75;
            foreach ($rows as $rowNo => $text) {
                $tspans .= $this->tag('tspan', [
                    'x' => $px,
                    'y' => $py,
                    'dy' => $dy + $rowNo * 1.4 . 'em',
                ], htmlspecialchars($text));
            }

            $source .= $this->tag('text', [
                'style' => 'font-size: ' . $this->fontSize . 'px',
            ], $tspans);

            $source .= "</g>\n";
        }

        return $source;
    }

    protected function renderLines(): string {
        $source = '';

        foreach ($this->items as $item) {
            // Parent
            if ($parent = $item->getParent()) {
                $source .= $this->tag('line', [
                    'class' => 'Parent',
                    'x1' => $item->px,
                    'y1' => $item->py,
                    'x2' => $parent->px,
                    'y2' => $parent->py,
                ]);
            }
        }

        return $source;
    }

    /**
     * @param string $name
     * @param array $attributes
     * @param string $content
     * @return string
     */
    protected function tag(string $name, array $attributes, string $content = ''): string {
        array_walk($attributes, function(&$value, $key) {
            $value = " $key=\"" . htmlspecialchars($value) . '"';
        });

            return "<$name" . implode($attributes) . ">$content</$name>\n";
    }
}