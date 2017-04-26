<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

use Fixin\Support\VariableInspector;

?><td class="Parameter Type">
    <?= ($parameter->isOptional() ? '?' : '') . (($class = $parameter->getClass()) ? $helper->reflectionLink($class) : ($parameter->getType() ?? $docParameters[$parameter->getName()] ?? '')) ?>
</td>
<td class="Parameter Name">
    <?= ($parameter->isVariadic() ? '...' : '') . ($parameter->isPassedByReference() ? '&' : '') . htmlspecialchars('$' . $parameter->getName()) ?>
</td>
<td class="Parameter Value"><?=
$parameter->isOptional() && $parameter->isDefaultValueAvailable()
    ? VariableInspector::valueInfo($parameter->getDefaultValue())
    : '';
?></td>
