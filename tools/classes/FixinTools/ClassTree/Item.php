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
use ReflectionClass;

class Item {

    /**
     * @var self[]
     */
    protected $children = [];

    /**
     * @var self
     */
    protected $parent;

    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @var ReflectionClass
     */
    protected $reflection;

    public function __construct(Processor $processor, ReflectionClass $reflection = null) {
        $this->processor = $processor;
        $this->reflection = $reflection;
    }

    public function __toString(): string {
        $result = "\n" . $this->getShortName() . "\n";

        foreach ($this->children as $child) {
            $result .= str_replace("\n", "\n    ", $child);
        }

        return $result;
    }

    public function addChild(Item $child): self {
        $child->removeFromParent();

        $this->children[$child->getName()] = $child;
        $child->parent = $this;

        return $this;
    }

    /**
     * @param string $name
     * @return Item|NULL
     */
    public function get(string $name) {
        return $this->children[$name] ?? null;
    }

    /**
     * @return self|NULL
     */
    public function getBelongsTo() {
        $namespace = $this->reflection->getNamespaceName();

        // Factory
        $factoryOf = Strings::isEndingWith($this->getName(), 'Factory') ? $this->processor->getItem(implode('\\', explode('\\', $this->reflection->getNamespaceName(), -1)) . '\\' . mb_substr($this->getShortName(), 0, -7)) : null;
        if ($factoryOf) {
            return $factoryOf;
        }

        // Main Class
        $mainClass = $this->processor->getMainClass($namespace);
        if ($mainClass && !$mainClass->isSubclassOf($this) && $mainClass->getName() !== $this->getName()) {
            return $mainClass;
        }

        // Parent
        return $this->processor->getMainClass(implode('\\', explode('\\', $namespace, -1)));
    }

    public function getChildren(): array {
        return $this->children;
    }

    public function getGroup(): string {
        $name = $this->getName();

        return substr($name, 0, strpos($name, '\\', strpos($name, '\\') + 1));
    }

    /**
     * @return Item
     */
    public function getImplementationOf() {
        $name = $this->getName();

        foreach ($this->getInterfaces() as $interface) {
            $interfaceName = $interface->name;
            if (Strings::isEndingWith($interfaceName, 'Interface') && $name === mb_substr($interfaceName, 0, -9)) {
                return $this->processor->getItem($interfaceName);
            }
        }

        return null;
    }

    public function getInterfaces(): array {
        $all = $this->reflection->getInterfaces();
        $interfaces = $all;

        while (count($all)) {
            $current = array_shift($all);

            if ($item = $this->processor->getItem($current->name)) {
                $itemInterfaces = $item->getInterfaces();
                $interfaces = array_diff_key($interfaces, $itemInterfaces);
                $all = array_diff_key($all, $itemInterfaces);
            }
        }

        return $interfaces;
    }

    public function getLevel(): int {
        return count(explode('\\', $this->getName())) - 1;
    }

    public function getName(): string {
        return $this->reflection->name;
    }

    /**
     * @return self|null
     */
    public function getParent() {
        return $this->parent;
    }

    public function getReflection(): ReflectionClass {
        return $this->reflection;
    }

    public function getShortName(): string {
        return $this->reflection->getShortName();
    }

    public function has(string $name): bool {
        return isset($this->children[$name]);
    }

    public function isAbstract(): bool {
        return $this->reflection->isAbstract();
    }

    public function isClass(): bool {
        return !$this->reflection->isInterface();
    }

    public function isDescendant(Item $item): bool {
        if (in_array($item, $this->children, true)) {
            return true;
        }

        foreach ($this->children as $child) {
            if ($child->isDescendant($item)) {
                return true;
            }
        }

        return false;
    }

    public function isException(): bool {
        return $this->reflection->isSubclassOf('Exception');
    }

    public function isFactory(): bool {
        return $this->reflection->isSubclassOf('Fixin\Resource\FactoryInterface');
    }

    public function isInterface(): bool {
        return $this->reflection->isInterface();
    }

    public function isMainClass(): bool {
        return $this->processor->getMainClass($this->reflection->getNamespaceName()) === $this;
    }

    public function isPrototype(): bool {
        return $this->reflection->isSubclassOf('Fixin\Resource\PrototypeInterface');
    }

    public function isResource(): bool {
        return $this->reflection->isSubclassOf('Fixin\Resource\ResourceInterface');
    }

    public function isSubclassOf(Item $item): bool {
        return $this->reflection->isSubclassOf($item->reflection);
    }

    public function isTrait(): bool {
        return $this->reflection->isTrait();
    }

    public function removeFromParent(): self {
        if ($this->parent) {
            unset($this->parent->children[$this->getName()]);
        }

        return $this;
    }

    public function unite(Item $item): self {
        $item->removeFromParent();
        $this->reflection = $item->reflection;

        foreach ($item->children as $child) {
            $this->addChild($child);
        }

        return $this;
    }
}
