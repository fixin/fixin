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
    font-size: 9pt;
    font-family: monospace;
}

a[href] {
    text-decoration: none;
    color: #06c;
}

table {
    border-collapse: collapse;
	border-spacing: 0;
	empty-cells: show;
	border: 1px solid #ddd;
}

tr.Element > td {
    padding-top: 2em;
}

tr.Element > td:first-child {
	padding-left: 2em;
}

td {
    padding: 0.4em 0.8em;
}

tr.ElementList table tr td {
    border-top: 1px solid #ddd;
}

tr.ElementList table tr:first-child td {
    border-top: none;
}

tr.ElementDetails > td:first-child,
tr.ElementList > td:first-child {
	padding-left: 4em;
}

pre {
    margin: 0;
}

.Name {
    font-weight: bold;
    min-wi2dth: 200px;
}

.Type {
    min-wid2th: 200px;
}

.Prefix {
    min-wid2th: 200px;
    text-align: right;
}

.Method .Prefix {
}
.Method > td > table {
    display: inline-block;
    vertical-align: middle;
}
		</style>
	</head>
	<body>
		<table>
			<?php foreach ($namespaces as $namespace => $elements): ?>
				<?php ksort($elements) ?>
				<tr>
					<td><h2><?= htmlspecialchars($namespace) ?></h2></td>
				</tr>
        		<?php foreach ($elements as $name): ?>
        			<?php $reflection = new ReflectionClass("$namespace\\$name"); ?>
        			<tr class="Element">
        				<td>
    						<span class="Name"><a name="<?= htmlspecialchars($reflection->name) ?>"><?= htmlspecialchars($reflection->name) ?></a></span>
						</td>
					</tr>
					<tr class="ElementDetails">
        				<td>
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
					<tr class="ElementList">
						<td>
							<table>
			    				<?php if ($constants = $reflection->getConstants()): ?>
			    					<?php ksort($constants) ?>
    								<?php foreach ($constants as $key => $value): ?>
    									<tr class="Const">
    										<td class="Prefix">const</td>
    										<td class="Name"><?= htmlspecialchars($key) ?></td>
    										<td colspan="3"><pre><?= VariableInspector::valueInfo($value) ?></pre></td>
    									</tr>
    								<?php endforeach ?>
    							<?php endif ?>
			    				<?php if (($properties = $reflection->getProperties($showProperties))): ?>
			    					<?php $defaultValues = $reflection->getDefaultProperties() ?>
    								<?php foreach (orderedReflectionList($properties) as $property): ?>
    									<?php if ($property->getDeclaringClass() == $reflection): ?>
        									<tr class="Property">
        										<td class="Prefix">
        											<?= $property->isPublic() ? 'public' : ($property->isProtected() ? 'protected' : 'private') ?>
        											<?= $property->isStatic() ? 'static' : '' ?>
        										</td>
    											<td class="Name">$<?= htmlspecialchars($property->getName()) ?></td>
    											<td><?= VariableInspector::valueInfo($defaultValues[$property->getName()] ?? null) ?></td>
    											<td><?= htmlspecialchars($property->getDocComment()) ?></td>
    											<td></td>
        									</tr>
    									<?php endif ?>
    								<?php endforeach ?>
			    				<?php endif ?>
			    				<?php if (($methods = $reflection->getMethods())): ?>
			    					<?php foreach (orderedReflectionList($methods) as $method): ?>
			    						<?php if ($method->getDeclaringClass() == $reflection): ?>
    			    						<tr class="Method">
    			    							<td class="Prefix">
    			    								<?= $method->isFinal() ? 'final' : '' ?>
    			    								<?= $method->isAbstract() ? 'abstract' : '' ?>
        											<?= $method->isPublic() ? 'public' : ($method->isProtected() ? 'protected' : 'private') ?>
        											<?= $method->isStatic() ? 'static' : '' ?>
        											function
        										</td>
        										<td class="Name"><?= htmlspecialchars($method->name) ?></td>
        										<td colspan="2">
        											(<?php if ($parameters = $method->getParameters()): ?>
        												<table>
                											<?php foreach ($parameters as $parameter): ?>
                												<tr class="Parameter">
                													<td class="Type">
                														<?php if ($class = $parameter->getClass()): ?>
    																		<?= reflectionLink($class) ?>
    																	<?php else: ?>
    																		<?= $parameter->getType() ?>
                														<?php endif ?>
                													</td>
                													<td class="Name">
                														<?= $parameter->isVariadic() ? '...' : ''
                														?><?= $parameter->isPassedByReference() ? '&' : ''
                														?>$<?= htmlspecialchars($parameter->getName()) ?>
            															<?php if ($parameter->isOptional()): ?>
            																=
            																<?php if ($parameter->isDefaultValueConstant()): ?>
            																	<?= htmlspecialchars($parameter->getDefaultValueConstantName()) ?>
            																<?php else: ?>
            																	<?= VariableInspector::valueInfo($parameter->getDefaultValue()) ?>
            																<?php endif ?>
            															<?php endif ?>
            														</td>
                												</tr>
                											<?php endforeach ?>
        												</table>
        											<?php endif ?>)
        										</td>
        										<td><?= $method->getReturnType() ?></td>
    										</tr>
										<?php endif ?>
			    					<?php endforeach ?>
			    				<?php endif ?>
							</table>
						</td>
					</tr>
        		<?php endforeach ?>
			<?php endforeach ?>
		</table>
	</body>
</html>