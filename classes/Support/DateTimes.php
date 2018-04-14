<?php
/**
 * /Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support;

class DateTimes extends DoNotCreate
{
    /**
     * Create date from expire time
     *
     * @param int $expireTime
     * @return \DateTimeImmutable
     */
    public static function fromExpireTime(int $expireTime): \DateTimeImmutable
    {
        return new \DateTimeImmutable("now +$expireTime seconds");
    }
}