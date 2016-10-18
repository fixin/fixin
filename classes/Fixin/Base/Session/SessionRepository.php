<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

class SessionRepository extends \Fixin\Model\Repository\SessionRepository {

    protected $primaryKey = ['sessionID'];

}