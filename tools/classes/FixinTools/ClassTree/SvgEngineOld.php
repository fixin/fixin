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

class SvgEngineOld
{


    protected function placeItems(array $items, float $x, float $y, float $r, float $angle, float $allowedAngle): void
    {
        $itemCount = count($items);
        $minR = $this->minRay($itemCount, $itemCount * $this->itemDistance);

        do {
            $r = max($r, $minR);

            $sizes = [];
            foreach ($items as $index => $item) {
                $sizes[$index] = $this->fetchSize($item, $r);
            }

            $width = array_sum($sizes);
            $minR = $this->minRay(count($sizes), $width);
        } while ($r < $minR);

        $nextR = $r + $this->itemDistance;
        $routeLength = $width - reset($sizes) / 2 - end($sizes) / 2;
//        $angle -= ($routeLength / $width * $allowedAngle) / 2;

        foreach ($items as $index => $item) {
            $itemAngle = $allowedAngle * $sizes[$index] / $width;
            $shifted = $angle + $itemAngle / 2;

            $item->px = ($x + cos($shifted * M_PI / 180) * $r) * $this->ratio;
            $item->py = ($y + sin($shifted * M_PI / 180) * $r * $this->ellipseRatio) * $this->ratio;
            $this->items[$item->getName()] = $item;

            if (!in_array($item->getName(), $this->stopAtClasses) && ($children = $item->getChildren())) {
                $this->placeItems($children, $x, $y, $nextR, $angle, $itemAngle);
            }

            $angle += $itemAngle;
        }
    }

    protected function minRay(int $count, float $length): float
    {
        if ($count <= 1) {
            return 0;
        }

        if ($count == 2) {
            return $this->itemDistance * 2;
        }

        return $length / 2 / M_PI;
    }

    protected function fetchSize(Item $item, $r): float
    {
        if (in_array($item->getName(), $this->stopAtClasses)) {
            return $this->itemDistance;
        }

        $childrenSize = 0;
        $nextR = $r + $this->itemDistance;

        foreach ($item->getChildren() as $child) {
            $childrenSize += $this->fetchSize($child, $nextR);
        }

        $ratio = $r > 0 ? $r / $nextR : 1;

        return max($this->itemDistance, $childrenSize * $ratio);
    }

    public function render(array $groups): string
    {
        $this->items = [];

        $itemGroups = $this->processor->getGroups();

        foreach ($groups as $name => $data) {
            $shiftAngle = $data['shiftAngle'] ?? 0;

            $this->placeItems($itemGroups[$name], $data['x'], $data['y'], 0, $shiftAngle, 360);
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
        $this->itemDistance = $itemSize * 2.4;
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
