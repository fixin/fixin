<?php
/**
 * Fixin Framework
 *
 * Class, interface, and trait lister
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */
namespace Classes;

class Helper {

    public $namespaces = [];
    protected $shortNameResolve = [];

    public function __construct(string $topDir, array $rootNamespaces = ['Fixin']) {
        // Include all PHP files under classes/
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator("$topDir/classes"));
        foreach ($iterator as $item) {
            if ($item->isFile() && strtolower($item->getExtension()) === 'php') {
                include_once $item;
            }
        }

        $this->processElements($rootNamespaces);
    }

    protected function processElements(array $rootNamespaces) {
        foreach (array_merge(get_declared_classes(), get_declared_interfaces(), get_declared_traits()) as $name) {
            if (($root = strstr($name, '\\', true)) && array_search($root, $rootNamespaces) !== false) {
                $namespace = substr($name, 0, strrpos($name, '\\'));
                $shortName = $this->classShortName($name);

                $this->namespaces[$namespace][] = $shortName;
                $this->shortNameResolve[$shortName] = $name;
            }
        };

        ksort($this->namespaces);
    }

    public function classShortName($name): string {
        return substr(strrchr($name, '\\'), 1);
    }

    public function classLink($name): string {
        $name = ltrim($name, '\\');

        $name = $this->shortNameResolve[$name] ?? $name;

        return strncmp($name, 'Fixin\\', 6)
        ? htmlspecialchars($name)
        : '<a href="#' . htmlspecialchars($name) . '">' . htmlspecialchars($this->classShortName($name)) . '</a>';
    }

    public function commentText($reflection) {
        if (preg_match_all('/^\s*\*\s*([^@\s*].+)$/m', $reflection->getDocComment(), $matches)) {
            if ($matches[1][0] === '{@inheritDoc}' && ($parent = $reflection->getPrototype())) {
                return '{@inheritDoc} ' . $this->commentText($parent);
            }
        }

        return nl2br(htmlspecialchars(implode("\n", $matches[1])));
    }

    public function commentParameters($reflection) {
        $parameters = [];

        preg_match_all('/^\s*\*\s*@param\s+([^\s]+)\s+\$([^\s]+)$/m', $reflection->getDocComment(), $matches);

        foreach ($matches[1] as $index => $type) {
            $parameters[$matches[2][$index]] = implode('|', array_map([$this, 'classLink'], (explode('|', $type))));
        }

        return $parameters;
    }

    public function commentVar($reflection) {
        if (preg_match_all('(@var\s+([^\s]+))', $reflection->getDocComment(), $matches)) {
            return implode('|', array_map([$this, 'classLink'], (explode('|', $matches[1][0]))));
        }

        return '';
    }

    public function evenStyle() {
        static $rowEvenState = 0;

        $rowEvenState = 1 - $rowEvenState;
        return $rowEvenState === 1 ? 'Even' : 'Odd';
    }

    public function reflectionLink($reflection): string {
        $name = $reflection->getName();

        return strncmp($name, 'Fixin\\', 6)
        ? '\\' . htmlspecialchars($name)
        : '<a href="#' . htmlspecialchars($name) . '">' . htmlspecialchars($reflection->getShortName()) . '</a>';
    }

    public function orderedReflectionList(array $reflections): array {
        $list = [];

        foreach ($reflections as $reflection) {
            $list[$reflection->getName()] = $reflection;
        }

        ksort($list);

        return $list;
    }
}