<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

$topDir = dirname(__DIR__, 2);
$application = include "$topDir/cheats/web.php";

// Functions
function reflectionLink($reflection): string {
    $name = $reflection->getName();

    return strncmp($name, 'Fixin\\', 6)
    ? '\\' . htmlspecialchars($name)
    : '<a href="#' . htmlspecialchars($reflection->name) . '">' . htmlspecialchars($reflection->getShortName()) . '</a>';
}

function orderedReflectionList(array $reflections): array {
    $list = [];

    foreach ($reflections as $reflection) {
        $list[$reflection->getName()] = $reflection;
    }

    ksort($list);

    return $list;
}

// Include all PHP files under classes/
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("$topDir/classes"));
foreach ($iterator as $item) {
    if ($item->isFile() && strtolower($item->getExtension()) === 'php') {
        include_once $item;
    }
}

// Defined Fixin elements
$namespaces = [];

foreach (array_merge(get_declared_classes(), get_declared_interfaces(), get_declared_traits()) as $name) {
    if (strncmp($name, 'Fixin\\', 6) === 0) {
        $x = strrpos($name, '\\');
        $namespaces[substr($name, 0, $x)][] = substr($name, $x + 1);
    }
};

ksort($namespaces);

use \Fixin\Support\VariableInspector;

$showProperties = empty($_GET['nonPublic'])
    ? ReflectionProperty::IS_PUBLIC
    : (ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

?><!DOCTYPE html>
<html>
	<head>
		<style>
body {
    font-size: 10pt;
    font-family: Lucida Grande;
}

table {
    border-collapse: collapse;
	border-spacing: 0;
	empty-cells: show;
}

td {
    padding: 0.4em 0.4em;
}

td.Tab {
    padding-left: 2em;
}

.Name {
    font-weight: bold;
}

.Value {
    white-space: pre;
    font-family: monospace;
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
    width: 1em;
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

		</style>
	</head>
	<body>
		<table>
			<?php foreach ($namespaces as $namespace => $elements): ?>
				<?php ksort($elements) ?>
				<tr class="Namespace">
					<td colspan="7"><h2><?= htmlspecialchars($namespace) ?></h2></td>
				</tr>
        		<?php foreach ($elements as $name): ?>
        			<?php $reflection = new ReflectionClass("$namespace\\$name"); ?>
        			<tr class="Element Header">
        				<td class="Tab"></td>
        				<td colspan="7">
    						<h3><a name="<?= htmlspecialchars($reflection->name) ?>"><?= htmlspecialchars($reflection->name) ?></a></h3>
						</td>
					</tr>
					<tr class="Element Details">
						<td class="Tab"></td>
						<td class="Tab"></td>
        				<td colspan="6">
    						<?php if ($reflection->isInterface()): ?>
        						interface
        					<?php elseif ($reflection->isTrait()): ?>
        						trait
        					<?php else: ?>
        						<?= $reflection->isFinal() ? 'final' : '' ?>
        						<?= $reflection->isAbstract() ? 'abstract' : '' ?>
        						class
        					<?php endif ?>

							<?php if ($parent = $reflection->getParentClass()): ?>
        						extends <?= reflectionLink($parent) ?>
        					<?php endif ?>

        					<?php if ($interfaces = $reflection->getInterfaces()): ?>
        						implements <?= implode(', ', array_map('reflectionLink', $interfaces)) ?>
        					<?php endif ?>

        					<?php if ($traits = $reflection->getTraits()): ?>
        						uses <?= implode(', ', array_map('reflectionLink', $traits)) ?>
        					<?php endif ?>
						</td>
    				</tr>
    				<?php if ($constants = $reflection->getConstants()): ?>
    					<?php ksort($constants) ?>
						<?php foreach ($constants as $key => $value): ?>
							<tr class="Element Const">
								<td class="Tab"></td>
								<td class="Tab"></td>
								<td>const</td>
								<td class="Name" colspan="4"><?= htmlspecialchars($key) ?></td>
								<td class="Value"><?= VariableInspector::valueInfo($value) ?></td>
							</tr>
						<?php endforeach ?>
					<?php endif ?>
					<?php if (($properties = $reflection->getProperties($showProperties))): ?>
    					<?php $defaultValues = $reflection->getDefaultProperties() ?>
						<?php foreach (orderedReflectionList($properties) as $property): ?>
							<?php if ($property->getDeclaringClass() == $reflection): ?>
								<tr class="Element Property">
									<td class="Tab"></td>
									<td class="Tab"></td>
									<td>
										<?= $property->isPublic() ? 'public' : ($property->isProtected() ? 'protected' : 'private') ?>
										<?= $property->isStatic() ? 'static' : '' ?>
									</td>
									<td class="Name" colspan="4">$<?= htmlspecialchars($property->getName()) ?></td>
									<td><?= VariableInspector::valueInfo($defaultValues[$property->getName()] ?? null) ?></td>
								</tr>
							<?php endif ?>
						<?php endforeach ?>
    				<?php endif ?>
    				<?php if (($methods = $reflection->getMethods())): ?>
    					<?php foreach (orderedReflectionList($methods) as $method): ?>
    						<?php if ($method->getDeclaringClass() == $reflection): ?>
    							<?php
                                    $parameters = $method->getParameters();
                                    $parameterCount = max(1, count($parameters));
    							?>
    							<tr class="Element Method">
    								<td class="Tab" rowspan="<?= $parameterCount ?>"></td>
    								<td class="Tab" rowspan="<?= $parameterCount ?>"></td>
	    							<td rowspan="<?= $parameterCount ?>">
	    								<?= $method->isFinal() ? 'final' : '' ?>
	    								<?= $method->isAbstract() ? 'abstract' : '' ?>
										<?= $method->isPublic() ? 'public' : ($method->isProtected() ? 'protected' : 'private') ?>
										<?= $method->isStatic() ? 'static' : '' ?>
										function
									</td>
									<td class="Name" rowspan="<?= $parameterCount ?>"><?= htmlspecialchars($method->name) ?></td>
									<?php if ($parameters): ?>
										<?php
										  $parameter = array_shift($parameters);
										  include 'classes.parameter.php';
                                        ?>
									<?php else: ?>
										<td colspan="3"></td>
									<?php endif ?>
									<td class="ReturnType" rowspan="<?= $parameterCount ?>"><?= $method->getReturnType() ?></td>
								</tr>
								<?php foreach ($parameters as $parameter): ?>
									<tr>
										<?php include 'classes.parameter.php' ?>
									</tr>
								<?php endforeach ?>
    						<?php endif ?>
						<?php endforeach ?>
					<?php endif ?>
        		<?php endforeach ?>
			<?php endforeach ?>
		</table>
	</body>
</html>