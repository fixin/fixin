<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace FixinTools\ClassTree;

class Processor extends Item {

    /**
     * @var SvgEngine
     */
    protected $engine;

    /**
     * @var Item[]
     */
    protected $items;

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

    public function __toString(): string {
         $info = '';

         foreach ($this->getGroups() as $name => $group) {
            $info .= "\n[$name]\n";
            foreach ($group as $item) {
                $info .= str_replace("\n", "\n    ", $item);
            }
         }

         return $info;
    }

    public function getEngine(): SvgEngine {
        return $this->engine ?? ($this->engine = new SvgEngine($this));
    }

    public function getGroups(): array {
        $groups = [];

        foreach ($this->children as $name => $item) {
            $groups[$item->getGroup()][] = $item;
        }

        return $groups;
    }

    /**
     * @param string $name
     * @return Item
     */
    public function getItem(string $name) {
        return $this->items[$name] ?? null;
    }

    /**
     * @param string $namespace
     * @return Item|null
     */
    public function getMainClass(string $namespace) {
        $tags = explode('\\', $namespace);
        $test = $namespace . '\\' . end($tags);

        return $this->items[$test . 'Interface']
        ?? $this->items[$test]
        ?? null;
    }

    public function hasItem(string $name): bool {
        return isset($this->items[$name]);
    }

    protected function processElements(array $baseClasses) {
        $this->processElementsItems();
        $this->processElementsTree($baseClasses);
    }

    protected function processElementsItems() {
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
    }

    protected function processElementsTree(array $baseClasses) {
        $this->children = [];
        foreach ($this->items as $name => $item) {
            // Extends class
            if (($parentClass = $item->getReflection()->getParentClass()) && (in_array($name, $baseClasses) || !in_array($parentClass->name, $baseClasses)) && isset($items[$parentClass->name])) {
                $this->items[$parentClass->name]->addChild($item);

                continue;
            }

            // Interface
            if ($interfaces = $item->getInterfaces()) {
                $interfaces = array_filter($interfaces, function($item) use ($name, $baseClasses) {
                    return (in_array($name, $baseClasses) || !in_array($item->name, $baseClasses)) && $this->hasItem($item->name);
                });

                if (count($interfaces)) {
                    $this->items[reset($interfaces)->name]->addChild($item);

                    continue;
                }
            }

            $this->children[$name] = $item;
        }
    }

    public function rearrangeForMap(): self {
        $this->uniteImplementations();
        $this->rearrangeToOwners();

        return $this;
    }

    protected function rearrangeToOwners() {
        $all = $this->items;
        while (count($all)) {
            $current = array_shift($all);

            if (!$current->getParent() && ($belongsTo = $current->getBelongsTo())) {
                $belongsTo->addChild($current);
                unset($this->children[$current->getName()]);
            }
        }
    }

    public function renderSvg(array $groups): string {
        return $this->getEngine()->render($groups);
    }

    protected function uniteImplementations() {
        $all = $this->items;
        while (count($all)) {
            $current = array_shift($all);

            if ($implementationOf = $current->getImplementationOf()) {
                $oldName = $implementationOf->getName();
                $newName = $current->getName();

                $implementationOf->unite($current);

                unset($this->items[$oldName]);
                unset($this->children[$oldName]);

                // Loop remove
                if ($implementationOf->isDescendant($current->getParent())) {
                    $this->uniteImplementationsRemoveLoop($current, $implementationOf);
                }

                $this->items[$newName] = $implementationOf;

                if (!$implementationOf->getParent()) {
                    $this->children[$implementationOf->getName()] = $implementationOf;
                }
            }
        }
    }

    protected function uniteImplementationsRemoveLoop(Item $current, Item $implementationOf) {
        $parent = $current->getParent();
        while ($parent !== $implementationOf) {
            $parent->removeFromParent();
            $parent = $parent->getParent();
        }
    }
}