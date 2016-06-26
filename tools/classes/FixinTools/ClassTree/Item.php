<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace FixinTools\ClassTree;

use Fixin\Support\Strings;

class Item {

    /**
     * @var self[]
     */
    protected $children = [];

    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @var self
     */
    protected $parent;

    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * @param Processor $processor
     * @param \ReflectionClass $reflection
     */
    public function __construct(Processor $processor, \ReflectionClass $reflection) {
        $this->processor = $processor;
        $this->reflection = $reflection;
    }

    /**
     * @return string
     */
    public function __toString(): string {
        $parentClass = $this->reflection->getParentClass();
        $info = "\n" . $this->getShortName() . "\n";

        foreach ($this->children as $index => $child) {
            $info .= str_replace("\n", "\n    ", $child);
        }

        return $info;
    }

    /**
     * @param self $child
     * @return self
     */
    public function addChild(self $child): self {
        $this->children[$child->getName()] = $child;
        $child->parent = $this;

        return $this;
    }

    /**
     * @return array
     */
    public function getChildren(): array {
        return $this->children;
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
                return $this->processor->get($interfaceName);
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getInterfaces(): array {
        $all = $this->reflection->getInterfaces();
        $interfaces = $all;

        while ($all) {
            $current = array_shift($all);

            if ($item = $this->processor->get($current->name)) {
                foreach ($item->getInterfaces() as $name => $interface) {
                    unset($interfaces[$name]);
                    unset($all[$name]);
                }
            }
        }

        return $interfaces;
    }

    /**
     * @return string
     */
    public function getGroup(): string {
        $tags = explode('\\', $this->getName(), 3);

        return $tags[1];
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->reflection->name;
    }

    /**
     * @return \FixinTools\ClassTree\self|null
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflection(): \ReflectionClass {
        return $this->reflection;
    }

    /**
     * @return string
     */
    public function getShortName(): string {
        return $this->reflection->getShortName();
    }

    /**
     * @return bool
     */
    public function isAbstract(): bool {
        return $this->reflection->isAbstract();
    }

    /**
     * @return bool
     */
    public function isClass(): bool {
        return !$this->reflection->isInterface();
    }

    /**
     * @param self $item
     * @return bool
     */
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

    /**
     * @return bool
     */
    public function isFactory(): bool {
        return $this->reflection->isSubclassOf('Fixin\Resource\Factory\FactoryInterface');
    }

    /**
     * @return bool
     */
    public function isInterface(): bool {
        return $this->reflection->isInterface();
    }

    /**
     * @return bool
     */
    public function isPrototype(): bool {
        return $this->reflection->isSubclassOf('Fixin\Resource\PrototypeInterface');
    }

    /**
     * @return bool
     */
    public function isResource(): bool {
        return $this->reflection->isSubclassOf('Fixin\Resource\ResourceInterface');
    }

    /**
     * @return self
     */
    public function removeFromParent(): self {
        if ($this->parent) {
            unset($this->parent->children[$this->getName()]);
        }

        return $this;
    }

    /**
     * @param self $item
     * @return self
     */
    public function unite(self $item): self {
        $item->removeFromParent();
        $this->reflection = $item->reflection;

        foreach ($item->children as $child) {
            $this->addChild($child);
        }

        return $this;
    }
}