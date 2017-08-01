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
use Fixin\Model\Entity\Entity;
use Fixin\Model\Entity\EntityInterface;

class SessionEntity extends Entity implements SessionEntityInterface
{
    /**
     * @var DateTimeImmutable
     */
    protected $accessTime;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $sessionId;

    public function collectSaveData(): array
    {
        return [
            'sessionId' => $this->sessionId,
            'data' => serialize($this->data),
            'accessTime' => $this->accessTime
        ];
    }

    /**
     * @return $this
     */
    public function exchangeArray(array $data): EntityInterface
    {
        $this->sessionId = $data['sessionId'] ?? null;

        $value = $data['data'] ?? null;
        $this->data = is_string($value) ? unserialize($value) : $value;

        $this->accessTime = $data['accessTime'] ?? null;

        return $this;
    }

    public function getAccessTime(): ?DateTimeImmutable
    {
        if (!$this->accessTime instanceof DateTimeImmutable && isset($this->accessTime)) {
            $this->accessTime = $this->getRepository()->toDateTime($this->accessTime);
        }

        return $this->accessTime;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    /**
     * @return $this
     */
    public function setAccessTime(?DateTimeImmutable $accessTime): SessionEntityInterface
    {
        $this->accessTime = $accessTime;

        return $this;
    }

    /**
     * @return $this
     */
    public function setData(array $data): SessionEntityInterface
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return $this
     */
    public function setSessionId(?string $sessionId): SessionEntityInterface
    {
        $this->sessionId = $sessionId;

        return $this;
    }
}
