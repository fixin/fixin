<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace FixinTools\ClassTree;

class Processor {

    /**
     * @var Item[]
     */
    protected $items;

    /**
     * @var Item[]
     */
    protected $tree;

    /**
     * @param string $topDir
     */
    public function __construct(string $topDir, array $baseClasses) {
        // Include all PHP files under classes/
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator("$topDir/classes"));
        foreach ($iterator as $item) {
            if ($item->isFile() && strtolower($item->getExtension()) === 'php') {
                include_once $item;
            }
        }

        $this->processElements($baseClasses);
    }

    /**
     * @return string
     */
    public function __toString(): string {
        $info = '';

        foreach ($this->tree as $item) {
            $info .= $item;
        }

        return $info;
    }

    /**
     * @param string $name
     * @return Item|NULL
     */
    public function get(string $name) {
        return $this->items[$name] ?? null;
    }

    /**
     * @return array
     */
    public function getTree(): array {
        return $this->tree;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool {
        return isset($this->items[$name]);
    }

    /**
     * @param array $baseClasses
     */
    protected function processElements(array $baseClasses) {
        // Build list
        $items = [];

        foreach (array_merge(get_declared_classes(), get_declared_interfaces(), get_declared_traits()) as $name) {
            $reflection = new \ReflectionClass($name);
            if ($reflection->isInternal() || mb_substr($name, 0, 11) === 'FixinTools\\') {
                continue;
            }

            $items[$reflection->name] = new Item($this, $reflection);
        }

        ksort($items);
        $this->items = $items;

        // Build tree
        $tree = [];

        foreach ($items as $name => $item) {
            // Extends class
            if (($parentClass = $item->getReflection()->getParentClass()) && !in_array($parentClass->name, $baseClasses) && isset($items[$parentClass->name])) {
                $items[$parentClass->name]->addChild($item);

                continue;
            }

            // Implements or extends interface
            if ($interfaces = $item->getInterfaces()) {
                $interfaces = array_filter($interfaces, function($item) use ($baseClasses) {
                    return !in_array($item->name, $baseClasses) && $this->has($item->name);
                });

                if ($interfaces) {
                    $items[reset($interfaces)->name]->addChild($item);

                    continue;
                }
            }

            $tree[$name] = $item;
        }

        $this->tree = $tree;
    }

    /**
     * @return \FixinTools\ClassTree\Processor
     */
    public function uniteInterfaceImplementations(): self {

        return $this;
    }
}