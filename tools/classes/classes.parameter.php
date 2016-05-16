<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

use \Fixin\Support\VariableInspector;

?><td>
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
</td>
<td>
	<?php if ($parameter->isOptional()): ?>
		<?php if ($parameter->isDefaultValueConstant()): ?>
			<?= htmlspecialchars($parameter->getDefaultValueConstantName()) ?>
		<?php else: ?>
			<?= VariableInspector::valueInfo($parameter->getDefaultValue()) ?>
		<?php endif ?>
	<?php endif ?>
</td>
