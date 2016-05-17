<?php
/**
 * Fixin Framework
 *
 * Class, interface, and trait lister
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

$topDir = dirname(__DIR__, 2);
$application = include "$topDir/cheats/web.php";

use \Fixin\Support\VariableInspector;

// Functions
include 'Helper.php';

$classes = new \Classes\Helper($topDir);

$showProperties = empty($_GET['all'])
    ? ReflectionProperty::IS_PUBLIC
    : (ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);
$showMethods = empty($_GET['all'])
    ? ReflectionMethod::IS_PUBLIC
    : (ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE);

?><!DOCTYPE html>
<html>
    <head>
        <style>
body {
    font-size: 9pt;
    font-family: monospace;
}

h1, h2, h3 {
    font-weight: bold;
    margin: 0;
}

h2 {
    font-size: 140%;
    line-height: 1.4em;
}

h3 {
    font-size: 120%;
    line-height: 1.4em;
}

a[href] {
    text-decoration: none;
    color: #06c;
}

table {
    border-collapse: separate;
    border-spacing: 0;
    empty-cells: show;
}

td {
    padding: 0.4em 0.4em;
}

td.Tab {
    padding-left: 2em;
}

.Details td {
    padding-bottom: 1em;
}

.Name {
}

.Value {
    white-space: pre;
    font-family: monospace;
}

.Comment {
    max-width: 40em;

    color: #579;
    line-height: 1.5;
    font-style: italic;
}

.FromComment {
    color: #999;
    font-style: italic;
}

.Parameter.Odd,
.Element.Odd > td:nth-child(n + 3) {
    background: #f8f8f8;
}

.Parameter.Even,
.Element.Even > td:nth-child(n + 3) {
    background: #f4f4f4;
}

.Const td:nth-child(n + 3),
.Property td:nth-child(n + 3),
.Method td:nth-child(n + 3) {
    border-top: 1px solid #ddd;
}

.Const td:nth-child(3),
.Property td:nth-child(3),
.Method td:nth-child(3) {
    border-left: 1px solid #ddd;
}

.Const td:last-child,
.Property td:last-child,
.Method td:last-child {
    border-right: 1px solid #ddd;
}

.Const .Name {
    color: #777;
}

.Method .Name {
    color: #070;
}

.Method .ReturnType,
.Method .Name {
    position: relative;
}

.Method .ReturnType:after,
.Method .Name:after {
    content: " ";

    border-width: 0;
    border-color: #000;
    border-style: solid;

    position: absolute;
    top: 0.4em;
    bottom: 0.4em;
    width: 0.75em;
}

.Method .Name:after {
    border-left-width: 0.2em;
    border-top-left-radius: 50%;
    border-bottom-left-radius: 50%;

    left: 100%;
}

.Method .ReturnType:after {
    border-right-width: 0.2em;
    border-top-right-radius: 50%;
    border-bottom-right-radius: 50%;

    right: 100%;
}

.Parameter.Type {
    padding-left: 1em;
}

.Parameter.Name:after {
    content: none;
}

.Parameter.Value {
    padding-right: 1em;
}

.Element + .Separator td:nth-child(n + 3) {
    border-top: 1px solid #ddd;
    padding-bottom: 2em
}

.Property .Name,
.Parameter.Name {
    color: #850;
}
        </style>
    </head>
    <body>
        <table>
            <?php foreach ($classes->namespaces as $namespace => $elements): ?>
                <?php ksort($elements) ?>
                <tr class="Namespace">
                    <td colspan="10"><h2><?= htmlspecialchars($namespace) ?></h2></td>
                </tr>
                <?php foreach ($elements as $name): ?>
                    <?php $reflection = new ReflectionClass("$namespace\\$name"); ?>
                    <tr class="Header">
                        <td class="Tab"></td>
                        <td colspan="9">
                            <h3><a name="<?= htmlspecialchars($reflection->name) ?>"><?= htmlspecialchars($reflection->name) ?></a></h3>
                        </td>
                    </tr>
                    <tr class="Details">
                        <td class="Tab"></td>
                        <td class="Tab"></td>
                        <td colspan="8">
                        	<?= $reflection->isInterface()
                                ? 'interface'
                                : ($reflection->isTrait()
                                    ? 'trait'
                                    : (($reflection->isFinal() ? 'final ' : '') . ($reflection->isAbstract() ? 'abstract ' : '') . 'class'));
                            ?>

                            <?php if ($parent = $reflection->getParentClass()): ?>
                                extends <?= $classes->reflectionLink($parent) ?>
                            <?php endif ?>

                            <?php if ($interfaces = $reflection->getInterfaces()): ?>
                                implements <?= implode(', ', array_map([$classes, 'reflectionLink'], $interfaces)) ?>
                            <?php endif ?>

                            <?php if ($traits = $reflection->getTraits()): ?>
                                uses <?= implode(', ', array_map([$classes, 'reflectionLink'], $traits)) ?>
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php if ($constants = $reflection->getConstants()): ?>
                        <?php ksort($constants); ?>
                        <?php foreach ($constants as $key => $value): ?>
                            <tr class="Element Const <?= $classes->evenStyle() ?>">
                                <td class="Tab"></td>
                                <td class="Tab"></td>
                                <td></td>
                                <td>const</td>
                                <td class="Name" colspan="4"><?= htmlspecialchars($key) ?></td>
                                <td class="Value" colspan="2"><?= VariableInspector::valueInfo($value) ?></td>
                            </tr>
                        <?php endforeach ?>
                        <tr class="Separator">
                            <td class="Tab"></td>
                            <td class="Tab"></td>
                            <td colspan="8"></td>
                        </tr>
                    <?php endif ?>
                    <?php if (($properties = $reflection->getProperties($showProperties))): ?>
                        <?php $defaultValues = $reflection->getDefaultProperties() ?>
                        <?php foreach ($classes->orderedReflectionList($properties) as $property): ?>
                            <?php if ($property->getDeclaringClass() == $reflection): ?>
                                <tr class="Element Property <?= $classes->evenStyle() ?>">
                                    <td class="Tab"></td>
                                    <td class="Tab"></td>
                                    <td>
                                        <?= $property->isPublic() ? 'public' : ($property->isProtected() ? 'protected' : 'private') ?>
                                        <?= $property->isStatic() ? 'static' : '' ?>
                                    </td>
                                    <td><?= $classes->commentVar($property) ?></td>
                                    <td class="Name" colspan="4">$<?= htmlspecialchars($property->getName()) ?></td>
                                    <td class="Value"><?= VariableInspector::valueInfo($defaultValues[$property->getName()] ?? null) ?></td>
                                    <td class="Comment"><?= $classes->commentText($property) ?></td>
                                </tr>
                            <?php endif ?>
                        <?php endforeach ?>
                        <tr class="Separator">
                            <td class="Tab"></td>
                            <td class="Tab"></td>
                            <td colspan="8"></td>
                        </tr>
                    <?php endif ?>
                    <?php if (($methods = $reflection->getMethods($showMethods))): ?>
                        <?php foreach ($classes->orderedReflectionList($methods) as $method): ?>
                            <?php if ($method->getDeclaringClass() == $reflection): ?>
                                <?php
                                    $parameters = $method->getParameters();
                                    $parameterCount = max(1, count($parameters));
                                    $docParameters = $classes->commentParameters($method);
                                ?>
                                <tr class="Element Method <?= $oddEvenStyle = $classes->evenStyle() ?>">
                                    <td class="Tab" rowspan="<?= $parameterCount ?>"></td>
                                    <td class="Tab" rowspan="<?= $parameterCount ?>"></td>
                                    <td rowspan="<?= $parameterCount ?>">
                                        <?= $method->isFinal() ? 'final' : '' ?>
                                        <?= $method->isAbstract() ? 'abstract' : '' ?>
                                        <?= $method->isPublic() ? 'public' : ($method->isProtected() ? 'protected' : 'private') ?>
                                        <?= $method->isStatic() ? 'static' : '' ?>
                                    </td>
                                    <td rowspan="<?= $parameterCount ?>">function</td>
                                    <td class="Name" rowspan="<?= $parameterCount ?>"><?= htmlspecialchars($method->name) ?></td>
                                    <?php if ($parameters): ?>
                                        <?php
                                            $parameter = array_shift($parameters);
                                            include 'classes.parameter.php';
                                        ?>
                                    <?php else: ?>
                                        <td colspan="3"></td>
                                    <?php endif ?>
                                    <td class="ReturnType" rowspan="<?= $parameterCount ?>">: <?= $method->getReturnType() ?? 'void' ?></td>
                                    <td class="Comment" rowspan="<?= $parameterCount ?>"><?= $classes->commentText($method) ?></td>
                                </tr>
                                <?php foreach ($parameters as $parameter): ?>
                                    <tr class="Element Parameter <?= $oddEvenStyle ?>">
                                        <?php include 'classes.parameter.php' ?>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        <?php endforeach ?>
                        <tr class="Separator">
                            <td class="Tab"></td>
                            <td class="Tab"></td>
                            <td colspan="8"></td>
                        </tr>
                    <?php endif ?>

                <?php endforeach ?>
            <?php endforeach ?>
        </table>
    </body>
</html>