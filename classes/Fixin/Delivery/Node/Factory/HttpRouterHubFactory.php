<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node\Factory;

use Fixin\Delivery\Node\HttpRouterHub;
use Fixin\Resource\Factory\Factory;
use Fixin\Support\Arrays;

class HttpRouterHubFactory extends Factory {

    const EXCEPTION_INVALID_ROUTE_ARGUMENT = "Invalid route argument for '%s'";
    const EXCEPTION_NO_ROUTES = "No routes";

    const OPTION_HANDLER = 'handler';
    const OPTION_PATTERNS = 'patterns';
    const OPTION_ROUTES = 'routes';
    const OPTION_URI = 'uri';

    /**
     * @var array
     */
    protected $handlers;

    /**
     * @var array
     */
    protected $patterns;

    /**
     * @var array
     */
    protected $routes;

    /**
     * @var string
     */
    protected $scopeHandler;

    /**
     * @var array
     */
    protected $scopePatterns;

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(array $options = NULL, string $name = NULL) {
        // Routes
        if (isset($options[static::OPTION_ROUTES])) {
            // Reset
            $this->routes = [];
            $this->handlers = [];
            $this->patterns = $options[static::OPTION_PATTERNS] ?? [];

            // Process
            $this->addRoutesFromDefinition($options[static::OPTION_ROUTES], '/', '');

            // Hub
            if (count($this->routes)) {
                return new HttpRouterHub($this->container, [
                    HttpRouterHub::OPTION_PARSED_ROUTES => $this->routes,
                    HttpRouterHub::OPTION_HANDLERS => $this->handlers
                ], $name);
            }
        }

        throw new RuntimeException(static::EXCEPTION_NO_ROUTES);
    }

    /**
     * Add route
     *
     * @param array $definition
     * @param string $uri
     * @throws InvalidArgumentException
     */
    protected function addRouteFromDefinition(array $definition, string $uri) {
        $this->scopePatterns = $this->patterns;

        if (isset($definition[static::OPTION_PATTERNS])) {
            $routePatterns = $definition[static::OPTION_PATTERNS];
            if (!is_array($routePatterns)) {
                throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_ROUTE_ARGUMENT, $uri));
            }

            $this->scopePatterns = array_replace($this->scopePatterns, $routePatterns);
        }

        $this->addRouteSegments(explode('/', trim($uri, '/')), [], [], 0);
    }

    /**
     * Add route group
     *
     * @param array $definition
     * @param string $uri
     * @param string $namespace
     * @throws InvalidArgumentException
     */
    protected function addRouteGroupFromDefinition(array $definition, string $uri, string $namespace) {
        foreach ($definition as $key => $route) {
            if (is_array($route)) {
                $this->addRoutesFromDefinition($route, $uri, $namespace . $key . '::');
                continue;
            }

            throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_ROUTE_ARGUMENT, $key));
        }
    }

    /**
     * Add route parameter segment
     *
     * @param string $name
     * @param array $segments
     * @param array $path
     * @param array $parameters
     * @param int $level
     */
    protected function addRouteParameterSegment(string $name, array $segments, array $path, array $parameters, int $level) {
        // Optional
        if ($name[strlen($name) - 1] === '?') {
            $this->addRouteSegments($segments, $path, $parameters, $level);

            $name = substr($name, 0, -1);
        }

        $pattern = HttpRouterHub::KEY_ANY_PARAMETER;
        $parameters[] = $name;

        if (isset($this->scopePatterns[$name])) {
            $pattern = $this->scopePatterns[$name];
            $path[] = HttpRouterHub::KEY_PATTERN_PARAMETER;
        }

        $path[] = $pattern;
        $this->addRouteSegments($segments, $path, $parameters, $level + 1);
    }

    /**
     * Add route segments
     *
     * @param array $segments
     * @param array $path
     * @param array $parameters
     * @param int $level
     */
    protected function addRouteSegments(array $segments, array $path, array $parameters, int $level) {
        // End
        if (empty($segments)) {
            array_unshift($path, $level);

            Arrays::set($this->routes, $path, [
                HttpRouterHub::KEY_HANDLER => $this->scopeHandler,
                HttpRouterHub::KEY_PARAMETERS => $parameters
            ]);

            return;
        }

        $segment = array_shift($segments);

        // Parameter
        if ($segment[0] === '{' && $segment[strlen($segment) - 1] === '}') {
            $this->addRouteParameterSegment(substr($segment, 1, -1), $segments, $path, $parameters, $level);

            return;
        }

        // Normal segment
        $path[] = $segment;
        $this->addRouteSegments($segments, $path, $parameters, $level + 1);
    }

    /**
     * Add routes
     *
     * @param array $definition
     * @param string $uri
     * @param string $namespace
     * @throws InvalidArgumentException
     */
    protected function addRoutesFromDefinition(array $definition, string $uri, string $namespace) {
        // Uri
        if (isset($definition[static::OPTION_URI])) {
            $uri = $this->uri($definition[static::OPTION_URI], $uri);

            unset($definition[static::OPTION_URI]);
        }

        // Route
        if (isset($definition[static::OPTION_HANDLER])) {
            $namespace = rtrim($namespace, ':');
            $this->scopeHandler = $namespace;
            $this->handlers[$namespace] = $definition[static::OPTION_HANDLER];

            $this->addRouteFromDefinition($definition, $uri);

            return;
        }

        // Group
        $this->addRouteGroupFromDefinition($definition, $uri, $namespace);
    }

    /**
     * URI overriding (absolute, relative)
     *
     * @param string $uri
     * @param string $inherited
     * @return string
     */
    protected function uri(string $uri, string $inherited): string {
        if ($uri !== '') {
            if ($uri[0] === '/') {
                $inherited = '';
            }

            return $inherited . rtrim($uri, '/') . '/';
        }

        return $inherited;
    }
}