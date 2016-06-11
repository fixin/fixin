<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Cookie;

use Fixin\Resource\PrototypeInterface;

interface CookieManagerInterface extends PrototypeInterface {

    const OPTION_COOKIES = 'cookies';

    /**
     * Get cookie value
     *
     * @param string $name
     * @return mixed
     */
    public function getValue(string $name);

    /**
     * Determine has value
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
}