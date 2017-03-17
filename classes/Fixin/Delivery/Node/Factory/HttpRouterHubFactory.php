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
use Fixin\Support\Strings;

class HttpRouterHubFactory extends Factory
{
    protected const
        EXCEPTION_INVALID_ROUTE_ARGUMENT = "Invalid route argument for '%s'",
        EXCEPTION_NO_ROUTES = "No routes";

    public const
        OPTION_HANDLER = 'handler',
        OPTION_PATTERNS = 'patterns',
        OPTION_ROUTES = 'routes',
        OPTION_URI = 'uri';

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
    protected $routeTree;

    /**
     * @var array
     */
    protected $routeUris;

    /**
     * @var string
     */
    protected $scopeName;

    /**
     * @var array
     */
    protected $scopePatterns;

    /**
     * @throws Exception\RuntimeException
     */
    public function __invoke(array $options = NULL, string $name = NULL): HttpRouterHub
    {
        // Routes
        if (isset($options[static::OPTION_ROUTES])) {
            // Reset
            $this->reset();
            $this->patterns = $options[static::OPTION_PATTERNS] ?? [];

            // Process
            $this->addRoutesFromDefinition($options[static::OPTION_ROUTES], '/', '');

            // Hub
            if ($this->routeTree) {
                return new HttpRouterHub($this->container, [
                    HttpRouterHub::OPTION_ROUTE_TREE => $this->routeTree,
                    HttpRouterHub::OPTION_ROUTE_URIS => $this->routeUris,
                    HttpRouterHub::OPTION_HANDLERS => $this->handlers
                ], $name);
            }
        }

        throw new Exception\RuntimeException(static::EXCEPTION_NO_ROUTES);
    }

    /**
     * @throws Exception\InvalidArgumentException
     */
    protected function addRouteFromDefinition(array $definition, string $uri): void
    {
        $this->scopePatterns = $this->patterns;

        if (isset($definition[static::OPTION_PATTERNS])) {
            $routePatterns = $definition[static::OPTION_PATTERNS];
            if (!is_array($routePatterns)) {
                throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_ROUTE_ARGUMENT, $uri));
            }

            $this->scopePatterns = array_replace($this->scopePatterns, $routePatterns);
        }

        $this->addRouteSegments(explode('/', trim($uri, '/')), [], '', [], 0);
    }

    /**
     * @throws Exception\InvalidArgumentException
     */
    protected function addRouteGroupFromDefinition(array $definition, string $uri, string $namespace): void
    {
        foreach ($definition as $key => $route) {
            if (is_array($route)) {
                $this->addRoutesFromDefinition($route, $uri, $namespace . $key . '::');
                continue;
            }

            throw new Exception\InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_ROUTE_ARGUMENT, $key));
        }
    }

    protected function addRouteItem(array $path, string $uri, array $parameters): void
    {
        Arrays::set($this->routeTree, $path, [
            HttpRouterHub::ROUTE_URI_HANDLER => $this->scopeName,
            HttpRouterHub::ROUTE_URI_PARAMETERS => array_keys($parameters)
        ]);

        $this->routeUris[$this->scopeName] = [
            HttpRouterHub::ROUTE_URI_URI => $uri,
            HttpRouterHub::ROUTE_URI_PARAMETERS => $parameters
        ];
    }

    protected function addRouteParameterSegment(string $name, array $segments, array $path, string $uri, array $parameters, int $level): void
    {
        // Optional
        $isOptional = $name[strlen($name) - 1] === '?';
        if ($isOptional) {
            $this->addRouteSegments($segments, $path, $uri, $parameters, $level);

            $name = substr($name, 0, -1);
        }

        $pattern = HttpRouterHub::ROUTE_URI_ANY_PARAMETER;
        $parameters[$name] = !$isOptional;

        if (isset($this->scopePatterns[$name])) {
            $pattern = $this->scopePatterns[$name];
            $path[] = HttpRouterHub::ROUTE_URI_PATTERN_PARAMETER;
        }

        $path[] = $pattern;
        $this->addRouteSegments($segments, $path, $uri . '%s', $parameters, $level + 1);
    }

    protected function addRouteSegments(array $segments, array $path, string $uri, array $parameters, int $level): void
    {
        // End
        if (empty($segments)) {
            array_unshift($path, $level);
            $this->addRouteItem($path, $uri, $parameters);

            return;
        }

        $segment = array_shift($segments);

        // Parameter
        if (Strings::surroundedBy($segment, '{', '}')) {
            $this->addRouteParameterSegment(substr($segment, 1, -1), $segments, $path, $uri, $parameters, $level);

            return;
        }

        // Normal segment
        $path[] = $segment;
        $this->addRouteSegments($segments, $path, $uri . '/' . str_replace('%', '%%', $segment), $parameters, $level + 1);
    }

    /**
     * @throws Exception\InvalidArgumentException
     */
    protected function addRoutesFromDefinition(array $definition, string $uri, string $namespace): void
    {
        // Uri
        if (isset($definition[static::OPTION_URI])) {
            $uri = $this->uri($definition[static::OPTION_URI], $uri);

            unset($definition[static::OPTION_URI]);
        }

        // Route
        if (isset($definition[static::OPTION_HANDLER])) {
            $namespace = rtrim($namespace, ':');
            $this->scopeName = $namespace;
            $this->handlers[$namespace] = $definition[static::OPTION_HANDLER];

            $this->addRouteFromDefinition($definition, $uri);

            return;
        }

        // Group
        $this->addRouteGroupFromDefinition($definition, $uri, $namespace);
    }

    /**
     * Reset data
     */
    protected function reset(): void
    {
        $this->routeTree = [];
        $this->routeUris = [];
        $this->handlers = [];
    }

    /**
     * URI overriding (absolute, relative)
     */
    protected function uri(string $uri, string $inherited): string
    {
        if ($uri !== '') {
            if ($uri[0] === '/') {
                $inherited = '';
            }

            return $inherited . rtrim($uri, '/') . '/';
        }

        return $inherited;
    }
}
