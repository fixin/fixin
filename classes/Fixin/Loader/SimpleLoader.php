<?php

namespace Fixin\Loader;

class SimpleLoader {

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
        $this->addNamespacePrefixes($prefixes);
    }

    /**
     * Adds a base path(s) for a prefix
     *
     * @param string $prefix
     * @param string|array $path
     * @return \Fixin\Loader\SimpleLoader
     */
    public function addNamespace($prefix, $path) {
        // Prepare prefix
        $prefix = trim($prefix, '\\');
        if (DIRECTORY_SEPARATOR !== '\\') {
            $prefix = strtr($prefix, '\\', DIRECTORY_SEPARATOR);
        }

        // Add normalized path(s)
        foreach (is_array($path) ? $path : [$path] as $item) {
            $this->paths[$prefix][] = rtrim($item, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        return $this;
    }

    /**
     * Adds multiple namespace prefixes
     *
     * @param array $prefixes
     * @return \Fixin\Loader\SimpleLoader
     */
    public function addNamespacePrefixes(array $prefixes) {
        foreach ($prefixes as $prefix => $path) {
            $this->addNamespace($prefix, $path);
        }

        return $this;
    }

    /**
     * Load class source file if exists
     *
     * @param string $class
     */
    public function autoload($class) {
        // Swap '\'
        if (DIRECTORY_SEPARATOR !== '\\') {
            $class = strtr($class, '\\', DIRECTORY_SEPARATOR);
        }

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
                        require $filename;

                        return;
                    }
                }

                return;
            }
        }
    }

    /**
     * Register to autoloader stack
     *
     * @return \Fixin\Loader\SimpleLoader
     */
    public function register() {
        spl_autoload_register(array($this, 'autoload'));

        return $this;
    }
}