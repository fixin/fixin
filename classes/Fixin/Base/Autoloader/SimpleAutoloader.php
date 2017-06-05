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

        // Fallback
        if (isset($this->paths[''])) {
            $relativeName = $class . '.php';

            // Search through the paths
            foreach ($this->paths[''] as $path) {
                if (file_exists($filename = $path . $relativeName)) {
                    fixinBaseAutoloaderEncapsulatedInclude($filename);

                    return;
                }
            }
        }
    }

    protected function register(): void
    {
        spl_autoload_register([$this, 'autoloadCallback']);
    }
}
