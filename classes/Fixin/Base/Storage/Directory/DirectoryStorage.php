<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Storage\Directory;

use Fixin\Base\FileSystem\FileSystemInterface;
use Fixin\Base\Model\RepositoryInterface;
use Fixin\Base\Storage\Storage;
use Fixin\Support\Strings;
use Fixin\Base\Exception\RuntimeException;

class DirectoryStorage extends Storage {

    const EXCEPTION_FILE_SYSTEM_NOT_SET = 'File system not set';
    const EXCEPTION_PATH_NOT_SET = 'Path not set';

    /**
     * @var FileSystemInterface|false|null
     */
    protected $fileSystem;

    /**
     * @var string
     */
    protected $path;

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Resource::configurationTests()
     */
    protected function configurationTests() {
        if (mb_strlen($this->path) === 0) {
            throw new RuntimeException(static::EXCEPTION_PATH_NOT_SET);
        }

        if (!isset($this->fileSystem)) {
            throw new RuntimeException(static::EXCEPTION_FILE_SYSTEM_NOT_SET);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Storage\StorageInterface::get($repository, $entityId)
     */
    public function get(RepositoryInterface $repository, $entityId) {
        $filename = $this->rowFilename($repository, $entityId);

        if ($this->fileSystem->isFile($filename)) {
            $contents = unserialize($this->fileSystem->get($filename));

            return $contents;
        }

        return null;
    }

    /**
     * @param RepositoryInterface $repository
     * @param string $entityId
     * @return string
     */
    protected function rowFilename(RepositoryInterface $repository, $entityId): string {
        return $this->path . $repository->getName() . DIRECTORY_SEPARATOR . $entityId . '.data';
    }

    /**
     * @param RepositoryInterface $repository
     * @param array $data
     * @param string $entityId
     * @return self
     */
    public function update(RepositoryInterface $repository, array $data, $entityId) {
        $contents = serialize($data);

        $this->fileSystem->put($this->rowFilename($repository, $entityId), $contents);

        return $this;
    }

    /**
     * Set data path
     *
     * @param string $path
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setPath(string $path) {
        $this->path = Strings::normalizePath($path);
    }
}