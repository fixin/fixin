<?php

namespace Fixin\Base\Autoloader;

require_once 'AutoloaderInterface.php';

class SimpleAutoloader implements AutoloaderInterface {

    /**
     * Registered paths
     *
     * @var array
     */
    protected $paths = [];

    /**
     * @param array $prefixes
     */
    public function __construct(array $prefixes = []) {
        $this->addPrefixes($prefixes);
    }

    /**
     * Adds base path(s) for a prefix
     *
     * @param string $prefix
     * @param string|array $path
     * @return \Fixin\Loader\SimpleLoader
     */
    public function addPrefixPath(string $prefix, $path) {
        // Prepare prefix
        $prefix = strtr(trim($prefix, '\\'), '\\', DIRECTORY_SEPARATOR);

        // Add normalized path(s)
        foreach (is_array($path) ? $path : [$path] as $item) {
            $this->paths[$prefix][] = rtrim($item, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        return $this;
    }

    /**
     * Adds multiple prefixes
     *
     * @param array $prefixes
     * @return \Fixin\Loader\SimpleLoader
     */
    public function addPrefixes(array $prefixes) {
        foreach ($prefixes as $prefix => $path) {
            $this->addPrefixPath($prefix, $path);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Autoloader\AutoloaderInterface::autoload()
     */
    public function autoload(string $class) {
        // Swap '\'
        $class = strtr($class, '\\', DIRECTORY_SEPARATOR);

        // Searching for prefix
        $length = 0;

        while (($length = strpos($class, DIRECTORY_SEPARATOR, $length + 1)) !== false) {
            $prefix = substr($class, 0, $length);

            // Prefix found
            if (isset($this->paths[$prefix])) {
                $relativeName = substr($class, $length + 1) . '.php';

                // Walking through the possible paths
                foreach ($this->paths[$prefix] as $path) {
                    $filename = $path . $relativeName;

                    // Source file found
                    if (file_exists($filename)) {
                        include $filename;

                        return;
                    }
                }

                return;
            }
        }
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Autoloader\AutoloaderInterface::register()
     */
    public function register() {
        spl_autoload_register([$this, 'autoload']);

        return $this;
    }
}