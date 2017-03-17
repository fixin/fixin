<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use DateTime;
use Fixin\Model\Entity\Entity;
use Fixin\Model\Entity\EntityInterface;

class SessionEntity extends Entity
{
    public const
        ACCESS_TIME = 'accessTime',
        SESSION_ID = 'sessionId';

    /**
     * @var DateTime
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
     * @return static
     */
    public function exchangeArray(array $data): EntityInterface
    {
        $this->sessionId = $data['sessionId'] ?? null;

        $value = $data['data'] ?? null;
        $this->data = is_string($value) ? unserialize($value) : $value;

        $this->accessTime = $data['accessTime'] ?? null;

        return $this;
    }

    public function getAccessTime(): ?DateTime
    {
        if (!$this->accessTime instanceof DateTime && isset($this->accessTime)) {
            $this->accessTime = $this->getRepository()->getValueAsDateTime($this->accessTime);
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
     * @return static
     */
    public function setAccessTime(?DateTime $accessTime): self
    {
        $this->accessTime = $accessTime;

        return $this;
    }

    /**
     * @return static
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return static
     */
    public function setSessionId(?string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }
}
