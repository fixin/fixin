<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

use \Fixin\Support\VariableInspector;

?><td class="Parameter Type">
	<?php if ($class = $parameter->getClass()): ?>
		<?= reflectionLink($class) ?>
	<?php else: ?>
		<?= $parameter->getType() ?>
	<?php endif ?>
</td>
<td class="Parameter Name">
	<?= $parameter->isVariadic() ? '...' : ''
	?><?= $parameter->isPassedByReference() ? '&' : ''
	?>$<?= htmlspecialchars($parameter->getName()) ?>
</td>
<td class="Parameter Value"><?=
$parameter->isOptional()
    ? ($parameter->isDefaultValueConstant()
        ? htmlspecialchars($parameter->getDefaultValueConstantName())
        : VariableInspector::valueInfo($parameter->getDefaultValue()))
    : '';
?></td>
