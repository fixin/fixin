<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
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
        $info = "\n" . $this->getShortName() . "\n";

        foreach ($this->children as $index => $child) {
            $info .= str_replace("\n", "\n    ", $child);
        }

        return $info;
    }

    public function addChild(self $child): self {
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
        if (Strings::endsWith($this->getName(), 'Factory')) {
            if ($factoryOf = $this->processor->getItem(implode('\\', explode('\\', $namespace, -1)) . '\\' . mb_substr($this->getShortName(), 0, -7))) {
                return $factoryOf;
            }
        }

        // Main Class
        $mainClass = $this->processor->getMainClass($namespace);
        if ($mainClass && $mainClass->getName() !== $this->getName()) {
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
     * @return self|null
     */
    public function getImplementationOf() {
        if (!$this->isClass()) {
            return null;
        }

        $name = $this->getName();
        foreach ($this->getInterfaces() as $interface) {
            $interfaceName = $interface->name;
            if (Strings::endsWith($interfaceName, 'Interface') && $name === mb_substr($interfaceName, 0, -9)) {
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
                foreach ($item->getInterfaces() as $name => $interface) {
                    unset($interfaces[$name]);
                    unset($all[$name]);
                }
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

    public function isDescendant(self $item): bool {
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
        return $this->reflection->isSubclassOf('Fixin\Resource\Factory\FactoryInterface');
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

    public function isSubclassOf(self $item): bool {
        return $this->reflection->isSubclassOf($item->reflection);
    }

    public function isTrait(): bool {
        return $this->reflection->isTrait();
    }

    public function removeFromParent(): self {
        if ($this->parent) {
            unset($this->parent->children[$this->getName()]);

            return $this;
        }

        return $this;
    }

    public function unite(self $item): self {
        $item->removeFromParent();
        $this->reflection = $item->reflection;

        foreach ($item->children as $child) {
            $this->addChild($child);
        }

        return $this;
    }
}