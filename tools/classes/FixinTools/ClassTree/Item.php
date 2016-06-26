<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace FixinTools\ClassTree;

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
        $info = "\n" . $this->getName() . ($parentClass ? ' [' . $parentClass->name . ']' : '') . "\n";

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
        $this->children[] = $child;
        $child->parent = $this;

        return $this;
    }

    public function getInterfaces() {
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
    public function getName(): string {
        return $this->reflection->name;
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflection(): \ReflectionClass {
        return $this->reflection;
    }
}