<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Session;

use DateTimeImmutable;
use Fixin\Model\Entity\EntityInterface;

interface SessionEntityInterface extends EntityInterface
{
    public const
        ACCESS_TIME = 'accessTime',
        SESSION_ID = 'sessionId';

    public function getAccessTime(): ?DateTimeImmutable;
    public function getData(): array;
    public function getSessionId(): ?string;

    /**
     * @return $this
     */
    public function setAccessTime(?DateTimeImmutable $accessTime): SessionEntityInterface;

    /**
     * @return $this
     */
    public function setData(array $data): SessionEntityInterface;

    /**
     * @return $this
     */
    public function setSessionId(?string $sessionId): SessionEntityInterface;
}
