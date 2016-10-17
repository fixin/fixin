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
    protected $sessionID;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::collectSaveData()
     */
    public function collectSaveData(): array {
        return [
            'sessionID' => $this->sessionID,
            'data' => serialize($this->data)
        ];
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Entity\EntityInterface::exchangeArray()
     */
    public function exchangeArray(array $data): EntityInterface {
        $this->sessionID = $data['sessionID'] ?? null;
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
     * Get session ID
     *
     * @return string
     */
    public function getSessionID() {
        return $this->sessionID;
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
     * Set session ID
     *
     * @param string $sessionID
     * @return self
     */
    public function setSessionID(string $sessionID): self {
        $this->sessionID = $sessionID;

        return $this;
    }
}