<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage;

use Countable;
use Fixin\Resource\PrototypeInterface;
use Traversable;

interface StorageResultInterface extends PrototypeInterface, Traversable, Countable {
}