<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Storage\Directory;

use Fixin\Base\FileSystem\FileSystemInterface;
use Fixin\Base\Model\RepositoryInterface;
use Fixin\Base\Storage\StorageInterface;
use Fixin\Resource\Resource;
use Fixin\Support\Strings;

class DirectoryStorage extends Resource implements StorageInterface {

    const CONFIGURATION_REQUIRES = [
        'fileSystem' => 'instance',
        'path' => 'string',
    ];
    const OPTION_PATH = 'path';

    /**
     * @var FileSystemInterface|false|null
     */
    protected $fileSystem;

    /**
     * @var string
     */
    protected $path = '';

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Storage\StorageInterface::get($repository, $key)
     */
    public function get(RepositoryInterface $repository, $key) {
        $filename = $this->rowFilename($repository, $key);

        if ($this->fileSystem->isFile($filename)) {
            $contents = unserialize($this->fileSystem->get($filename));

            return $contents;
        }

        return null;
    }

    /**
     * @param RepositoryInterface $repository
     * @param string $key
     * @return string
     */
    protected function rowFilename(RepositoryInterface $repository, $key): string {
        return $this->path . $repository->getName() . DIRECTORY_SEPARATOR . $key . '.data';
    }

    /**
     * @param RepositoryInterface $repository
     * @param array $data
     * @param string $key
     * @return self
     */
    public function update(RepositoryInterface $repository, array $data, $key) {
        $contents = serialize($data);

        $this->fileSystem->put($this->rowFilename($repository, $key), $contents);

        return $this;
    }

    /**
     * Set data path
     *
     * @param string $path
     */
    protected function setPath(string $path) {
        $this->path = Strings::normalizePath($path);
    }
}