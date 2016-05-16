<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

function classShortName($name): string {
    return substr(strrchr($name, '\\'), 1);
}

function classLink($name): string {
    $name = ltrim($name, '\\');
    return strncmp($name, 'Fixin\\', 6)
    ? htmlspecialchars($name)
    : '<a href="#' . htmlspecialchars($name) . '">' . htmlspecialchars(classShortName($name)) . '</a>';
}

function commentText($reflection) {
    if (preg_match_all('/^\s*\*\s*([^@\s*].+)$/m', $reflection->getDocComment(), $matches)) {
        if ($matches[1][0] === '{@inheritDoc}' && ($parent = $reflection->getPrototype())) {
            return '{@inheritDoc} ' . commentText($parent);
        }
    }

    return nl2br(htmlspecialchars(implode("\n", $matches[1])));
}

function commentParameters($reflection) {
    $parameters = [];

    preg_match_all('/^\s*\*\s*@param\s+([^\s]+)\s+\$([^\s]+)$/m', $reflection->getDocComment(), $matches);

    foreach ($matches[1] as $index => $type) {
        $parameters[$matches[2][$index]] = implode('|', array_map('classLink', (explode('|', $type))));
    }

    return $parameters;
}

function commentVar($reflection) {
    if (preg_match_all('(@var\s+([^\s]+))', $reflection->getDocComment(), $matches)) {
        return implode('|', array_map('classLink', (explode('|', $matches[1][0]))));
    }

    return '';
}

function evenStyle() {
    static $rowEvenState = 0;

    $rowEvenState = 1 - $rowEvenState;
    return $rowEvenState === 1 ? 'Even' : 'Odd';
}

function reflectionLink($reflection): string {
    $name = $reflection->getName();

    return strncmp($name, 'Fixin\\', 6)
    ? '\\' . htmlspecialchars($name)
    : '<a href="#' . htmlspecialchars($name) . '">' . htmlspecialchars($reflection->getShortName()) . '</a>';
}

function orderedReflectionList(array $reflections): array {
    $list = [];

    foreach ($reflections as $reflection) {
        $list[$reflection->getName()] = $reflection;
    }

    ksort($list);

    return $list;
}
