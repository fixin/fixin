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

    /**
     * @var array
     */
    protected $stopAtClasses = [];

    public function __construct(Processor $processor) {
        $this->processor = $processor;
    }

    protected function calculateFontSize() {
        $this->fontSize = $this->ratio * 0.26 * $this->itemSize;
    }

    protected function explodeRows(string $text): array {
        $words = explode(' ', $text);

        $rows = [];
        $buffer = array_shift($words);

        foreach ($words as $word) {
            if (mb_strlen($buffer . $word) < 12) {
                $buffer .= ' ' . $word;
                continue;
            }

            $rows[] = $buffer;
            $buffer = $word;
        }

        $rows[] = $buffer;

        return $rows;
    }

    protected function itemCssClass(Item $item): string {
        $classes = ['Item'];
        foreach (['Interface', 'Abstract', 'Prototype', 'Resource', 'Factory', 'Exception', 'Trait'] as $test) {
            if ($item->{'is' . $test}()) {
                $classes[] = $test;
            }
        }

        return str_replace('Interface Abstract', 'Interface', implode(' ', $classes));
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
            'class' => 'Center',
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

            if (!in_array($item->getName(), $this->stopAtClasses) && ($children = $item->getChildren())) {
                $childrenStep = min(max($angleStep / 1.3, 160 / count($children) / 1.3), 180);

                $this->placeItems($children, $px, $py, $angle - $childrenStep, $angle + $childrenStep);
            }

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

        return $this->renderGroups($groups) . $this->renderLines() . $this->renderItems();
    }

    protected function renderGroups(array $groups): string {
        $source = '';

        foreach ($groups as $data) {
            if (isset($data['label'])) {
                $source .= $this->tag('text', [
                    'class' => 'Center Top',
                    'x' => ($data['x'] + ($data['labelDx'] ?? 0)) * $this->ratio,
                    'y' => ($data['y'] + ($data['labelDy'] ?? 0)) * $this->ratio,
                    'dy' => '0.4em'
                ], htmlspecialchars($data['label']));
            }
        }

        return $source;
    }

    protected function renderItems(): string {
        $source = '';
        $itemR = $this->itemSize * $this->ratio;

        foreach ($this->items as $item) {
            $px = $item->px;
            $py = $item->py;

            $source .= '<g class="' . $this->itemCssClass($item) . "\">\n"
                . $this->tag('ellipse', ['cx' => $px, 'cy' => $py, 'rx' => $itemR, 'ry' => $itemR * $this->ellipseRatio])
                . $this->itemText($px, $py, Strings::textFromCamelCase($item->getShortName()))
                . "</g>\n";
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

    public function setStopAtClasses(array $stopAtClasses): self {
        $this->stopAtClasses = $stopAtClasses;

        return $this;
    }

    protected function tag(string $name, array $attributes, string $content = ''): string {
        array_walk($attributes, function(&$value, $key) {
            $value = " $key=\"" . htmlspecialchars($value) . '"';
        });

            return "<$name" . implode($attributes) . ">$content</$name>\n";
    }
}