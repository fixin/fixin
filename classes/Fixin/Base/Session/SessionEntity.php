<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use DateTime;
use Fixin\Model\Entity\EntityInterface;

class SessionEntity extends \Fixin\Model\Entity\Entity {

    const
    COLUMN_ACCESS_TIME = 'accessTime',
    COLUMN_SESSION_ID = 'sessionId';

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

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::collectSaveData()
     */
    public function collectSaveData(): array {
        return [
            'sessionId' => $this->sessionId,
            'data' => serialize($this->data),
            'accessTime' => $this->accessTime
        ];
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::exchangeArray()
     */
    public function exchangeArray(array $data): EntityInterface {
        $this->sessionId = $data['sessionId'] ?? null;

        $value = $data['data'] ?? null;
        $this->data = is_string($value) ? unserialize($value) : $value;

        $this->accessTime = $data['accessTime'] ?? null;

        return $this;
    }

    /**
     * Get access time
     *
     * @return DateTime
     */
    public function getAccessTime() {
        if (!$this->accessTime instanceof DateTime && isset($this->accessTime)) {
            $this->accessTime = $this->getRepository()->valueToDateTime($this->accessTime);
        }

        return $this->accessTime;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array {
        return $this->data;
    }

    /**
     * Get session id
     *
     * @return string
     */
    public function getSessionId() {
        return $this->sessionId;
    }

    /**
     * Set access time
     *
     * @param DateTime $accessTime
     * @return self
     */
    public function setAccessTime(DateTime $accessTime): self {
        $this->accessTime = $accessTime;

        return $this;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return self
     */
    public function setData(array $data): self {
        $this->data = $data;

        return $this;
    }

    /**
     * Set session id
     *
     * @param string $sessionId
     * @return self
     */
    public function setSessionId(string $sessionId): self {
        $this->sessionId = $sessionId;

        return $this;
    }
}