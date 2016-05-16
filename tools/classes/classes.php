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
	border: 1px solid #ddd;
}

		</style>
	</head>
	<body>
		<table>
			<?php foreach ($namespaces as $namespace => $elements): ?>
				<?php ksort($elements) ?>
				<tr>
					<td colspan="6"><h2><?= htmlspecialchars($namespace) ?></h2></td>
				</tr>
        		<?php foreach ($elements as $name): ?>
        			<?php $reflection = new ReflectionClass("$namespace\\$name"); ?>
        			<tr class="Element">
        				<td colspan="6">
    						<h3><a name="<?= htmlspecialchars($reflection->name) ?>"><?= htmlspecialchars($reflection->name) ?></a></h3>
						</td>
					</tr>
					<tr class="ElementDetails">
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
							<tr class="Const">
								<td>const</td>
								<td colspan="4" class="Name"><?= htmlspecialchars($key) ?></td>
								<td><?= VariableInspector::valueInfo($value) ?></td>
							</tr>
						<?php endforeach ?>
					<?php endif ?>
					<?php if (($properties = $reflection->getProperties($showProperties))): ?>
    					<?php $defaultValues = $reflection->getDefaultProperties() ?>
						<?php foreach (orderedReflectionList($properties) as $property): ?>
							<?php if ($property->getDeclaringClass() == $reflection): ?>
								<tr class="Property">
									<td>
										<?= $property->isPublic() ? 'public' : ($property->isProtected() ? 'protected' : 'private') ?>
										<?= $property->isStatic() ? 'static' : '' ?>
									</td>
									<td colspan="4" class="Name">$<?= htmlspecialchars($property->getName()) ?></td>
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
    							<tr class="Method">
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
                                        <td rowspan="<?= $parameterCount ?>"><?= $method->getReturnType() ?></td>
									<?php else: ?>
										<td colspan="4"></td>
									<?php endif ?>
								</tr>
								<?php
								    foreach ($parameters as $parameter) {
								        include 'classes.parameter.php';
								    }
								?>
    						<?php endif ?>
						<?php endforeach ?>
					<?php endif ?>
        		<?php endforeach ?>
			<?php endforeach ?>
		</table>
	</body>
</html>