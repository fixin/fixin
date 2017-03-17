<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use Fixin\Model\Repository\Repository;

class SessionRepository extends Repository
{
    protected $primaryKey = ['sessionId'];
}
