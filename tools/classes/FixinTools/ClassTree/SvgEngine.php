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
    protected $itemSize;

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

    public function __construct(Processor $processor) {
        $this->processor = $processor;
    }

    protected function calculateFontSize() {
        $this->fontSize = $this->ratio * 0.28 * $this->itemSize;
    }

    protected function explodeRows(string $text): array {
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

    public function itemText(float $x, float $y, string $text): string {
        $rows = $this->explodeRows($text);
        $tspans = '';
        $dy = -count($rows) * 0.7 + 0.25 + 0.75;

        foreach ($rows as $rowNo => $text) {
            $tspans .= $this->tag('tspan', [
                'x' => $x,
                'y' => $y,
                'dy' => $dy + $rowNo * 1.4 . 'em',
            ], htmlspecialchars($text));
        }

        return $this->tag('text', [
            'style' => 'font-size: ' . $this->fontSize . 'px',
        ], $tspans);
    }

    protected function placeItems(array $items, float $x, float $y, float $startAngle, float $endAngle) {
        $divs = count($items);
        $angleStep = ($endAngle - $startAngle) / $divs;
        $angle = $startAngle + $angleStep / 2;
        $itemR = max($this->itemSize * 2.6, $divs * ($this->itemSize * 2.4) / M_PI / 2 * 360 / ($endAngle - $startAngle));

        foreach ($items as $item) {
            $px = $x + cos($angle * M_PI / 180) * $itemR;
            $py = $y + sin($angle * M_PI / 180) * $itemR * $this->ellipseRatio;

            $item->px = $px * $this->ratio;
            $item->py = $py * $this->ratio;

            $this->items[$item->getName()] = $item;

            if ($children = $item->getChildren()) {
                $childrenStep = min(max($angleStep / 1.3, 160 / count($children) / 1.3), 180);
                $childrenStart = $angle - $childrenStep;
                $childrenEnd = $angle + $childrenStep;

                $this->placeItems($children, $px, $py, $childrenStart, $childrenEnd);
            }

            // Step
            $angle += $angleStep;
        }
    }

    public function render(array $groups): string {
        $itemGroups = $this->processor->getGroups();

        $this->items = [];
        foreach ($groups as $name => $data) {
            $shiftAngle = $data['shiftAngle'] ?? 0;
            $this->placeItems($itemGroups[$name], $data['x'], $data['y'], $shiftAngle + 0, $shiftAngle + 360);
        }

        return $this->renderLines() . $this->renderItems();
    }

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
            $source .= $this->itemText($px, $py, Strings::textFromCamelCase($item->getShortName()));

            // Close
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
                    'class' => $item->isSubclassOf($parent) ? 'Parent' : 'Owner',
                    'x1' => $item->px,
                    'y1' => $item->py,
                    'x2' => $parent->px,
                    'y2' => $parent->py,
                ]);
            }
        }

        return $source;
    }

    public function setEllipseRatio(float $ellipseRatio): self {
        $this->ellipseRatio = $ellipseRatio;

        return $this;
    }

    public function setItemSize(float $itemSize): self {
        $this->itemSize = $itemSize;
        $this->calculateFontSize();

        return $this;
    }

    public function setRatio(int $ratio): self {
        $this->ratio = $ratio;
        $this->calculateFontSize();

        return $this;
    }

    protected function tag(string $name, array $attributes, string $content = ''): string {
        array_walk($attributes, function(&$value, $key) {
            $value = " $key=\"" . htmlspecialchars($value) . '"';
        });

            return "<$name" . implode($attributes) . ">$content</$name>\n";
    }
}