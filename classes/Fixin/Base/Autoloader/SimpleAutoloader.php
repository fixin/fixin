<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Autoloader;

require_once 'AutoloaderInterface.php';

class SimpleAutoloader implements AutoloaderInterface
{
    /**
     * Registered paths
     *
     * @var array[]
     */
    protected $paths = [];

    /**
     * @param array $prefixes
     */
    public function __construct(array $prefixes = [])
    {
        foreach ($prefixes as $prefix => $path) {
            $this->addPrefixPath($prefix, $path);
        }

        $this->register();
    }

    /**
     * Add base path(s) for a prefix
     */
    protected function addPrefixPath(string $prefix, $path): void
    {
        // Prepare prefix
        $prefix = strtr(trim($prefix, '\\'), '\\', DIRECTORY_SEPARATOR);

        // Add normalized path(s)
        foreach (is_array($path) ? $path : [$path] as $item) {
            $this->paths[$prefix][] = rtrim($item, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }
    }

    public function autoload(string $class): void
    {
        // Swap '\'
        $class = strtr($class, '\\', DIRECTORY_SEPARATOR);

        // Searching for prefix
        $length = 0;

        while ($length = strpos($class, DIRECTORY_SEPARATOR, $length + 1)) {
            $prefix = substr($class, 0, $length);

            // Prefix found
            if (isset($this->paths[$prefix])) {
                $relativeName = substr($class, $length + 1) . '.php';

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

    /**
     * Register autoload function
     */
    protected function register(): void
    {
        spl_autoload_register([$this, 'autoload']);
    }
}
