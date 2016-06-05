<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Model;

use Fixin\Base\Storage\StorageInterface;
use Fixin\Exception\InvalidArgumentException;
use Fixin\Exception\RuntimeException;
use Fixin\Resource\Resource;

class Repository extends Resource implements RepositoryInterface {

    const EXCEPTION_INVALID_NAME = "Invalid name '%s'";
    const EXCEPTION_NAME_NOT_SET = "Name not set";
    const EXCEPTION_PRIMARY_KEY_NOT_SET = 'Primary key not set';
    const EXCEPTION_STORAGE_NOT_SET = "Storage not set";

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string[]
     */
    protected $primaryKey = ['id'];

    /**
     * @var StorageInterface|false|null
     */
    protected $storage;

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests(): Resource {
        if ($this->name === '') {
            throw new RuntimeException(static::EXCEPTION_NAME_NOT_SET);
        }

        if (!isset($this->storage)) {
            throw new RuntimeException(static::EXCEPTION_STORAGE_NOT_SET);
        }

        if (empty($this->primaryKey)) {
            throw new RuntimeException(static::EXCEPTION_PRIMARY_KEY_NOT_SET);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Model\RepositoryInterface::get($key)
     */
    public function get($key) {
        $data = $this->getStorage()->get($this, $key);
        return $data;
    }

    /**
     * Get name of the repository
     *
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Get storage instance
     *
     * @return StorageInterface
     */
    protected function getStorage(): StorageInterface {
        return $this->storage ?: $this->loadLazyProperty('storage');
    }

    /**
     * Set name
     *
     * @param string $name
     * @throws InvalidArgumentException
     */
    protected function setName(string $name) {
        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
            $this->name = $name;

            return;
        }

        throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_NAME, $name));
    }

    /**
     * Set primary key
     *
     * @param string[] $primaryKey
     */
    protected function setPrimaryKey(array $primaryKey) {
        $this->primaryKey = $primaryKey;
    }

    /**
     * Set storage
     *
     * @param string|StorageInterface $storage
     */
    protected function setStorage($storage) {
        $this->setLazyLoadingProperty('storage', StorageInterface::class, $storage);
    }
}