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

class SvgEngine
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

    public function itemWidth(Item $item): float
    {
        $childWidth = 0;
        foreach ($item->getChildren() as $child) {
            if (!in_array($item->getName(), $this->stopAtClasses) && ($children = $item->getChildren())) {
                $childWidth += $this->itemWidth($child) * 0.6;
            }
        }

        return max($this->itemDistance, $childWidth);
    }

    protected function placeItems(array $items, float $x, float $y, float $angle, float $allowedAngle): void
    {
        $itemCount = count($items);
        $nextAllowedAngle = $itemCount > 1 ? 180 : $allowedAngle;

        $r = $allowedAngle == 360 && $itemCount === 1 ? 0 : $this->itemDistance;
        $minR = $this->minRay($itemCount,$itemCount * $this->itemDistance, $allowedAngle);

        do {
            $r = max($r, $minR);

            $widths = [];
            foreach ($items as $index => $item) {
                $widths[$index] = $this->itemWidth($item);
            }

            $fullWidth = array_sum($widths);
            $minR = $this->minRay($itemCount, $fullWidth, $allowedAngle);
        } while ($r < $minR);

        $maxWidth = 2 * $r * M_PI * $allowedAngle / 360;
        $usedAngle = $allowedAngle == 360 ? $allowedAngle : ($allowedAngle * $fullWidth / $maxWidth);
        $angle -= $usedAngle / 2;

        foreach ($items as $index => $item) {
            $itemAngle = $usedAngle * $widths[$index] / $fullWidth;
            $angle += $itemAngle / 2;

            $px = $x + cos($angle * M_PI / 180) * $r;
            $py = $y + sin($angle * M_PI / 180) * $r * $this->ellipseRatio;

            $item->px = $px * $this->ratio;
            $item->py = $py * $this->ratio;
            $this->items[$item->getName()] = $item;

            if (!in_array($item->getName(), $this->stopAtClasses) && ($children = $item->getChildren())) {
                $this->placeItems($children, $px, $py, $angle, $nextAllowedAngle);
            }

            $angle += $itemAngle / 2;
        }
    }

    protected function minRay(int $itemCount, float $length, float $allowedAngle): float
    {
        if ($itemCount < 2) {
            return 0;
        }

        return $length / 2 / M_PI * 360 / $allowedAngle;
    }

    public function render(array $groups): string
    {
        $this->items = [];

        $itemGroups = $this->processor->getGroups();

        foreach ($groups as $name => $data) {
            $this->placeItems($itemGroups[$name], $data['x'], $data['y'], $data['shiftAngle'] ?? 0, 360);
        }

        return $this->renderGroups($groups) . $this->renderLines() . $this->renderItems();
    }

    protected function renderGroups(array $groups): string
    {
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
