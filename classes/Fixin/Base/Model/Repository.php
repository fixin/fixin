<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Model;

use Fixin\Resource\Resource;
use Fixin\Base\Storage\StorageInterface;
use Fixin\Base\Exception\InvalidArgumentException;

class Repository extends Resource implements RepositoryInterface {

    const EXCEPTION_INVALID_NAME = "Invalid name '%s'";
    const EXCEPTION_INVALID_STORAGE_TYPE = 'Invalid storage type';
    const EXCEPTION_NO_NAME_DEFINED = "No name defined";
    const EXCEPTION_NO_PRIMARY_KEY_DEFINED = 'No primary key defined';
    const EXCEPTION_NO_STORAGE_SET = "No storage set";

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string[]
     */
    protected $primaryKey = ['id'];

    /**
     * @var EntityInterface|string
     */
    protected $prototypeEntity = '\Fixin\Base\Model\Entity';

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configureWithOptions()
     */
    protected function configureWithOptions(array $options) {
        parent::configureWithOptions($options);

        if (mb_strlen($this->name) === 0) {
            throw new InvalidArgumentException(static::EXCEPTION_NO_NAME_DEFINED);
        }

        if (!isset($this->storage)) {
            throw new InvalidArgumentException(static::EXCEPTION_NO_STORAGE_SET);
        }

        if (empty($this->primaryKey)) {
            throw new InvalidArgumentException(static::EXCEPTION_NO_PRIMARY_KEY_DEFINED);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Model\RepositoryInterface::get($id)
     */
    public function get($id) {
        $data = $this->storage->get($this, $id);
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
     * Set storage
     *
     * @param string|StorageInterface $storage
     * @throws InvalidArgumentException
     */
    protected function setStorage($storage) {
        if (is_string($storage)) {
            $this->storage = $this->container->get($storage);

            return;
        }

        if ($storage instanceof StorageInterface) {
            $this->storage = $storage;

            return;
        }

        throw new InvalidArgumentException(static::EXCEPTION_INVALID_STORAGE_TYPE);
    }
}