<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Autoloader;

require_once 'AutoloaderInterface.php';

class SimpleAutoloader implements AutoloaderInterface
{
    /**
     * @var array[]
     */
    protected $paths = [];

    /**
     * @var int
     */
    protected $precutLength = 0;

    public function __construct(array $prefixes = [])
    {
        foreach ($prefixes as $prefix => $path) {
            $this->addPrefixPath($prefix, $path);
        }

        $this->register();
    }

    protected function addPrefixPath(string $prefix, $path): void
    {
        // Prepare prefix
        $prefix = strtr(trim($prefix, '\\'), '\\', DIRECTORY_SEPARATOR);
        $this->precutLength = max($this->precutLength, strlen($prefix) + 1);

        // Add normalized path(s)
        foreach ((array) $path as $item) {
            $this->paths[$prefix][] = rtrim($item, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }
    }

    public function autoloadCallback(string $class): void
    {
        // Swap '\'
        $class = strtr($class, '\\', DIRECTORY_SEPARATOR);

        // Searching for prefix
        $prefix = substr($class, 0, $this->precutLength) . 'a';

        while ($prefix) {
            $prefix = dirname($prefix);
            if ($prefix === '.') {
                $prefix = '';
            }

            // Prefix found
            if (isset($this->paths[$prefix])) {
                $relativeName = substr($class, strlen($prefix) + 1) . '.php';

                // Search through the paths
                foreach ($this->paths[$prefix] as $path) {
                    if (file_exists($filename = $path . $relativeName)) {
                        fixinBaseAutoloaderEncapsulatedInclude($filename);

                        return;
                    }
                }

                return;
            }
        }
    }

    protected function register(): void
    {
        spl_autoload_register([$this, 'autoloadCallback']);
    }
}
