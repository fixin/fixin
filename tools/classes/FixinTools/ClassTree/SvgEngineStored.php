<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace FixinTools\ClassTree;

use Fixin\Support\Strings;

class SvgEngineStored
{
    /**
     * @var float
     */
    protected $ellipseRatio;

    /**
     * @var float
     */
    protected $fontSize;

    protected $itemDistance;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var float
     */
    protected $itemSize;

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

    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    protected function calculateFontSize(): void
    {
        $this->fontSize = $this->ratio * 0.26 * $this->itemSize;
    }

    protected function explodeRows(string $text): array
    {
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

    protected function itemCssClass(Item $item): string
    {
        $classes = ['Item'];
        foreach (['Interface', 'Abstract', 'Prototype', 'Resource', 'Factory', 'Exception', 'Trait'] as $test) {
            if ($item->{'is' . $test}()) {
                $classes[] = $test;
            }
        }

        return str_replace('Interface Abstract', 'Interface', implode(' ', $classes));
    }

    public function itemText(float $x, float $y, string $text): string
    {
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

    protected function itemWidth(Item $item): float
    {
        return $this->itemDistance;
        $min = 0;
        $max = 0;

        $this->findMinMax($item, $min, $max);

        return $max - $min;
    }

    protected function findMinMax(Item $item, float &$min, float &$max): void
    {
        $min = min($min, $item->px - $this->itemDistance / 2);
        $max = max($max, $item->px + $this->itemDistance / 2);

        foreach ($item->getChildren() as $child) {
            if (isset($child->px)) {
                $this->findMinMax($child, $min, $max);
            }
        }
    }

    protected function placeItems(array $items, float $allowedAngle): void
    {
        $nextAllowedAngle = count($items) > 1 ? 180 : $allowedAngle;

        foreach ($items as $item) {
            if (!in_array($item->getName(), $this->stopAtClasses)) {
                $this->placeItems($item->getChildren(), $nextAllowedAngle);
            }

            $this->items[$item->getName()] = $item;
            $item->px = 0;
            $item->py = 0;
        }

        $widths = [];
        foreach ($items as $index => $item) {
            $widths[$index] = $this->itemWidth($item);
        }
        $fullWidth = array_sum($widths);

        $r = max($this->itemDistance, $this->minRay($fullWidth, $allowedAngle));

        $maxWidth = 2 * $r * M_PI * $allowedAngle / 360;

        $usedAngle = $allowedAngle === 360 ? $allowedAngle : ($allowedAngle * $fullWidth / $maxWidth);

        $angle = - $usedAngle / 2;

        foreach ($items as $index => $item) {
            $itemAngle = $usedAngle * $widths[$index] / $fullWidth;
            $angle += $itemAngle / 2;

            $rad = ($angle + 90) / 180 * M_PI;
            $dx = cos($rad) * $r;
            $dy = sin($rad) * $r;

            $this->transformItem($item, $angle, $dx, $dy);

            $angle += $itemAngle / 2;
        }
    }

    protected function transformItem(Item $item, float $angle, float $dx, float $dy): void
    {
        $rad = $angle / 180 * M_PI;

        $this->transformItemTree($item, cos($rad), sin($rad), $dx, $dy);
    }

    protected function transformItemTree(Item $item, float $cos, float $sin, float $dx, float $dy): void
    {
        $x = $item->px;
        $y = $item->py;

        $item->px = $x * $cos - $y * $sin + $dx;
        $item->py = $x * $sin + $y * $cos + $dy;

        foreach ($item->getChildren() as $child) {
            if (isset($child->px)) {
                $this->transformItemTree($child, $cos, $sin, $dx, $dy);
            }
        }
    }

    protected function placeItemGroup(array $items, float $angle, float $dx, float $dy): void
    {
        foreach ($items as $item) {
            $this->transformItem($item, $angle, $dx, $dy);
        }
    }

    protected function minRay(float $length, float $allowedAngle): float
    {
        return $length / 2 / M_PI * 360 / $allowedAngle;
    }

    public function render(array $groups): string
    {
        $this->items = [];

        $itemGroups = $this->processor->getGroups();

        foreach ($groups as $name => $data) {
            $this->placeItems($itemGroups[$name], 360);
            $this->placeItemGroup($itemGroups[$name], $data['shiftAngle'] ?? 0, $data['x'], $data['y']);
        }

        foreach ($this->items as $item) {
            $item->px *= $this->ratio;
            $item->py *= $this->ratio * $this->ellipseRatio;
        }

        return $this->renderLines() . $this->renderItems();
    }

    protected function renderItems(): string
    {
        $source = '';
        $itemR = $this->itemSize * $this->ratio;

        foreach ($this->items as $item) {
            $px = $item->px;
            $py = $item->py;

            $source .= '<g class="' . $this->itemCssClass($item) . "\">\n"
                . $this->tag('ellipse', ['cx' => $px, 'cy' => $py, 'rx' => $itemR, 'ry' => $itemR * $this->ellipseRatio])
                . $this->itemText($px, $py, Strings::camelCasedToText($item->getShortName()))
                . "</g>\n";
        }

        return $source;
    }

    protected function renderLines(): string
    {
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

    public function setEllipseRatio(float $ellipseRatio): self
    {
        $this->ellipseRatio = $ellipseRatio;

        return $this;
    }

    public function setItemSize(float $itemSize): self
    {
        $this->itemSize = $itemSize;
        $this->itemDistance = $itemSize * 2.6;
        $this->calculateFontSize();

        return $this;
    }

    public function setRatio(int $ratio): self
    {
        $this->ratio = $ratio;
        $this->calculateFontSize();

        return $this;
    }

    public function setStopAtClasses(array $stopAtClasses): self
    {
        $this->stopAtClasses = $stopAtClasses;

        return $this;
    }

    protected function tag(string $name, array $attributes, string $content = ''): string
    {
        $list = [];

        foreach ($attributes as $key => $value) {
            $list[] = " $key=\"" . htmlspecialchars($value) . '"';
        }

        return "<$name" . implode($list) . ">$content</$name>\n";
    }
}
