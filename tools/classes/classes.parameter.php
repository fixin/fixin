<?php
/**
 * Fixin Framework
 *
 * Class, interface, and trait lister
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

use \Fixin\Support\VariableInspector;

?><td class="Parameter Type">
    <?= ($class = $parameter->getClass()) ? $classes->reflectionLink($class) : ($parameter->getType() ?? $docParameters[$parameter->getName()] ?? '') ?>
</td>
<td class="Parameter Name">
    <?= ($parameter->isVariadic() ? '...' : '') . ($parameter->isPassedByReference() ? '&' : '') . htmlspecialchars('$' . $parameter->getName()) ?>
</td>
<td class="Parameter Value"><?=
$parameter->isOptional()
    ? ($parameter->isDefaultValueConstant()
        ? htmlspecialchars($parameter->getDefaultValueConstantName())
        : VariableInspector::valueInfo($parameter->getDefaultValue()))
    : '';
?></td>
