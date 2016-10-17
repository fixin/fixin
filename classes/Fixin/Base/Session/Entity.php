<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

class Entity extends \Fixin\Model\Entity\Entity {

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
            'data' => serialize($this->data)
        ];
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::exchangeArray()
     */
    public function exchangeArray(array $data): EntityInterface {
        $this->sessionId = $data['sessionId'] ?? null;
        $this->data = isset($data['data']) ? (is_string($data['data']) ? unserialize($data['data']) : $data['data']) : null;

        return $this;
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