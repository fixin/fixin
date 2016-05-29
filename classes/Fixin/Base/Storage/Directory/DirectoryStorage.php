<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Storage\Directory;

use Fixin\Base\Model\Entity\EntityInterface;
use Fixin\Base\Model\Repository\RepositoryInterface;
use Fixin\Support\Strings;

class DirectoryStorage extends Storage {

    /**
     * @var string
     */
    protected $extension = '.data';

    /**
     * @var string[]
     */
    protected $indexedColumns = ['id'];

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string[]
     */
    protected $primaryKey = ['id'];

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Storage\StorageInterface::get($repository, $id)
     */
    public function get(RepositoryInterface $repository, $id): EntityInterface {
        $filename = $this->rowFilename($repository, $id);

        if (is_file($filename)) {
            $content = file_get_contents($filename);
        }

        return null;
    }

    /**
     * @param RepositoryInterface $repository
     * @param string $id
     * @return string
     */
    protected function rowFilename(RepositoryInterface $repository, $id): string {
        return $this->path . $repository->getName() . DIRECTORY_SEPARATOR . $id . $extension;
    }

    /**
     * @param RepositoryInterface $repository
     * @param string $id
     * @param array $data
     * @return self
     */
    public function update(RepositoryInterface $repository, $id, array $data) {
        $content = serialize($data);

        file_put_contents($this->rowFilename($repository, $id), $content);

        return $this;
    }

    /**
     * Set file extension
     *
     * @param string $extension
     */
    protected function setExtension(string $extension) {
        $this->extension = '.' . ltrim($extension, '.');
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