<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Storage;

use Countable;
use Fixin\Resource\PrototypeInterface;
use Iterator;

interface StorageResultInterface extends PrototypeInterface, Iterator, Countable
{
}
