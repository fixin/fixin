<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Session;

use Fixin\Model\Repository\Repository;

class SessionRepository extends Repository
{
    protected $primaryKey = ['sessionId'];
}
