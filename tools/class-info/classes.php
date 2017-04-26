<?php
/**
 * Fixin Framework
 * Class, interface, and trait lister
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

$topDir = dirname(__DIR__, 2);
$resourceManager = include "$topDir/cheats/tools.php";

use Fixin\Base\FileSystem\FileSystemInterface;use Fixin\Support\VariableInspector;

// Load
$resourceManager->get('defaultFileSystem', FileSystemInterface::class)->includeFilesRecursive("$topDir/classes", 'php');

// Functions
$helper = new \FixinTools\Base\ClassHelper();
$showAll = !empty($_GET['all']) || !empty($all);

$showProperties = $showAll ? (ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE) : ReflectionProperty::IS_PUBLIC;
$showMethods = $showAll ? (ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE) : ReflectionMethod::IS_PUBLIC;

?><!DOCTYPE html>
<html>
    <head>
        <style>
#classes {
    font-size: 10pt;
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
}

#classes h1, h2, h3 {
    font-weight: bold;
    margin: 0;
}

#classes h2 {
    font-size: 140%;
    line-height: 1.4em;
}

#classes h3 {
    font-size: 120%;
    line-height: 1.4em;
}

#classes a[href] {
    text-decoration: none;
    color: #06c;
}

#classes table {
    border-collapse: separate;
    border-spacing: 0;
    empty-cells: show;
}

#classes td {
    padding: 0.4em 0.4em;
}

#classes td.Tab {
    padding-left: 2em;
}

#classes .Details td {
    padding-bottom: 1em;
}

#classes .Value {
    white-space: pre-wrap;
    font-family: monospace;
}

#classes .Comment {
    max-width: 40em;

    color: #579;
    line-height: 1.5;
    font-style: italic;
}

#classes .FromComment {
    color: #999;
    font-style: italic;
}

#classes .Inherited {
    color: #aaa;
}

#classes .Element td {
    color: #686868;
}

#classes .Parameter.Odd,
#classes .Element.Odd > td:nth-child(n + 3) {
    background: #f8f8f8;
}

#classes .Parameter.Even,
#classes .Element.Even > td:nth-child(n + 3) {
    background: #f4f4f4;
}

#classes .Const td:nth-child(n + 3),
#classes .Property td:nth-child(n + 3),
#classes .Method td:nth-child(n + 3) {
    border-top: 1px solid #ddd;
}

#classes .Const td:nth-child(3),
#classes .Property td:nth-child(3),
#classes .Method td:nth-child(3) {
    border-left: 1px solid #ddd;
}

#classes .Const td:last-child,
#classes .Property td:last-child,
#classes .Method td:last-child {
    border-right: 1px solid #ddd;
}

#classes .Public .Visibility {
    color: #484;
}

#classes .Protected .Visibility {
    color: #b90;
}

#classes .Private .Visibility {
    color: #d70;
}

#classes .Method .ReturnType,
#classes .Method .Name {
    position: relative;
}

#classes .Method .ReturnType:after,
#classes .Method .Name:after {
    content: " ";

    border-width: 0;
    border-color: #000;
    border-style: solid;

    position: absolute;
    top: 0.4em;
    bottom: 0.4em;
    width: 0.75em;
}

#classes .Method .Name:after {
    border-left-width: 0.2em;
    border-top-left-radius: 50%;
    border-bottom-left-radius: 50%;

    left: 100%;
}

#classes .Method .ReturnType:after {
    border-right-width: 0.2em;
    border-top-right-radius: 50%;
    border-bottom-right-radius: 50%;

    right: 100%;
}

#classes .Parameter.Type {
    padding-left: 1em;
}

#classes .Parameter.Name:after {
    content: none;
}

#classes .Parameter.Value {
    padding-right: 1em;
}

#classes .Element + .Separator td:nth-child(n + 3) {
    border-top: 1px solid #ddd;
    padding-bottom: 2em
}
        </style>
    </head>
    <body>
        <div id="classes">
            <table>
                <?php foreach ($helper->namespaces as $namespace => $elements): ?>
                    <?php ksort($elements) ?>
                    <tr class="Namespace">
                        <td colspan="10"><h2><?= htmlspecialchars($namespace) ?></h2></td>
                    </tr>
                    <?php foreach ($elements as $name => $reflection): ?>
                        <tr class="Header" id="<?= htmlspecialchars(strtr($reflection->name, '\\', '-')) ?>">
                            <td class="Tab"></td>
                            <td colspan="9">
                                <h3><?= htmlspecialchars($reflection->name) ?></h3>
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
                                    extends <?= $helper->reflectionLink($parent) ?>
                                <?php endif ?>

                                <?php if ($interfaces = $reflection->getInterfaces()): ?>
                                    implements <?= implode(', ', array_map([$helper, 'reflectionLink'], $interfaces)) ?>
                                <?php endif ?>

                                <?php if ($traits = $reflection->getTraits()): ?>
                                    uses <?= implode(', ', array_map([$helper, 'reflectionLink'], $traits)) ?>
                                <?php endif ?>
                            </td>
                        </tr>
                        <?php
                            if ($constants = $reflection->getConstants()) {
                                $inheriteds = [];
                                $parent = $reflection;

                                while ($parent = $parent->getParentClass()) {
                                    $inheriteds += $parent->getConstants();
                                }

                                $constants = array_filter($constants, function($value, $key) use ($inheriteds) {
                                    return !isset($inheriteds[$key]) || $inheriteds[$key] !== $value;
                                }, ARRAY_FILTER_USE_BOTH);

                                ksort($constants);
                            }
                        ?>
                        <?php if (!empty($constants)): ?>

                            <?php foreach ($constants as $key => $value): ?>
                                <tr class="Element Const <?= $helper->evenStyle() ?> Public">
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
                            <?php foreach ($helper->orderedReflectionList($properties) as $property): ?>
                                <?php if ($property->getDeclaringClass() == $reflection): ?>
                                    <tr class="Element Property <?= $helper->evenStyle() ?> <?= ucfirst($visibility = $helper->reflectionVisibility($property)) ?>">
                                        <td class="Tab"></td>
                                        <td class="Tab"></td>
                                        <td class="Visibility">
                                            <?= $visibility ?>
                                            <?= $property->isStatic() ? 'static' : '' ?>
                                        </td>
                                        <td><?= $helper->commentVar($property) ?></td>
                                        <td class="Name" colspan="4">$<?= htmlspecialchars($property->getName()) ?></td>
                                        <td class="Value"><?= VariableInspector::valueInfo($defaultValues[$property->getName()] ?? null) ?></td>
                                        <td class="Comment"><?= $helper->commentText($property) ?></td>
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
                            <?php foreach ($helper->orderedReflectionList($methods) as $method): ?>
                                <?php if ($method->getDeclaringClass() == $reflection): ?>
                                    <?php
                                        $parameters = $method->getParameters();
                                        $parameterCount = max(1, count($parameters));
                                        $docParameters = $helper->commentParameters($method);
                                        $returnType = $method->getReturnType();
                                    ?>
                                    <tr class="Element Method <?= $oddEvenStyle = $helper->evenStyle() ?> <?= ucfirst($visibility = $helper->reflectionVisibility($method)) ?>">
                                        <td class="Tab" rowspan="<?= $parameterCount ?>"></td>
                                        <td class="Tab" rowspan="<?= $parameterCount ?>"></td>
                                        <td class="Visibility" rowspan="<?= $parameterCount ?>">
                                            <?= $method->isFinal() ? 'final' : '' ?>
                                            <?= !$reflection->isInterface() && $method->isAbstract() ? 'abstract' : '' ?>
                                            <?= $visibility ?>
                                            <?= $method->isStatic() ? 'static' : '' ?>
                                        </td>
                                        <td rowspan="<?= $parameterCount ?>">function</td>
                                        <td class="Name" rowspan="<?= $parameterCount ?>"><?= htmlspecialchars($method->name) ?></td>
                                        <?php if ($parameters): ?>
                                            <?php
                                                $parameter = array_shift($parameters);
                                                include 'includes/classes.parameter.php';
                                            ?>
                                        <?php else: ?>
                                            <td colspan="3"></td>
                                        <?php endif ?>
                                        <td class="ReturnType" rowspan="<?= $parameterCount ?>"><?= rtrim(': ' . ($returnType ? ($returnType->allowsNull() ? '?' : '') . $helper->classLink($returnType) : $helper->commentReturnType($method)), ': ') ?></td>
                                        <td class="Comment" rowspan="<?= $parameterCount ?>"><?= $helper->commentText($method) ?></td>
                                    </tr>
                                    <?php foreach ($parameters as $parameter): ?>
                                        <tr class="Element Parameter <?= $oddEvenStyle ?> <?= ucfirst($visibility) ?>">
                                            <?php include 'includes/classes.parameter.php' ?>
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
        </div>
    </body>
</html>
