<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage;

use Countable;
use Fixin\Resource\PrototypeInterface;
use Iterator;

interface StorageResultInterface extends PrototypeInterface, Iterator, Countable {
}