<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Storage\Pdo;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Model\Repository\RepositoryRequestInterface;
use Fixin\Model\Storage\StorageInterface;
use Fixin\Resource\Resource;
use Fixin\Model\Storage\StorageResultInterface;

class PdoStorage extends Resource implements StorageInterface {

    const OPTION_DSN = 'dsn';
    const OPTION_PASSWORD = 'password';
    const OPTION_USERNAME = 'username';

    /**
     * @var string
     */
    protected $dsn;

    /**
     * @var GrammarInterface
     */
    protected $grammar;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var \PDO
     */
    protected $resource;

    /**
     * @var string
     */
    protected $username;

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::delete($request)
     */
    public function delete(RepositoryRequestInterface $request): int {
        return $this->execute($this->grammar->delete($request));
    }

    protected function execute(string $script): int {
        // todo
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::getLastInsertValue()
     */
    public function getLastInsertValue(): int {
        return $this->resource->lastInsertId();
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::insert($repository, $set)
     */
    public function insert(RepositoryInterface $repository, array $set): int {
        return $this->execute($this->grammar->insert($repository, $set));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::insertInto($repository, $request)
     */
    public function insertInto(RepositoryInterface $repository, RepositoryRequestInterface $request): int {
        return $this->execute($this->grammar->insertInto($repository, $request));
    }

    protected function query(string $script): StorageResultInterface {
        // todo
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::select($request)
     */
    public function select(RepositoryRequestInterface $request): StorageResultInterface {
        return $this->query($this->grammar->select($request));
    }

    /**
     * Set dsn
     *
     * @param string $dsn
     */
    protected function setDsn(string $dsn) {
        $this->dsn = $dsn;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    protected function setPassword(string $password) {
        $this->password = $password;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    protected function setUsername(string $username) {
        $this->username = $username;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Storage\StorageInterface::update($set, $request)
     */
    public function update(array $set, RepositoryRequestInterface $request): int {
        return $this->execute($this->grammar->update($set, $request));
    }
}