<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Node\Factory;

use Fixin\Delivery\Node\HttpRouterHub;
use Fixin\Resource\Factory;
use Fixin\Resource\FactoryInterface;
use Fixin\Support\Arrays;
use Fixin\Support\Strings;

class HttpRouterHubFactory extends Factory implements FactoryInterface
{
    protected const
        INVALID_ROUTE_ARGUMENT_EXCEPTION = "Invalid route argument for '%s'",
        NO_ROUTES_EXCEPTION = "No routes";

    public const
        HANDLER = 'handler',
        PATTERNS = 'patterns',
        ROUTES = 'routes',
        URI = 'uri';

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
        if (isset($options[static::ROUTES])) {
            // Reset
            $this->reset();
            $this->patterns = $options[static::PATTERNS] ?? [];

            // Process
            $this->addRoutesFromDefinition($options[static::ROUTES], '/', '');

            // Hub
            if ($this->routeTree) {
                return new HttpRouterHub($this->resourceManager, [
                    HttpRouterHub::ROUTE_TREE => $this->routeTree,
                    HttpRouterHub::ROUTE_URIS => $this->routeUris,
                    HttpRouterHub::HANDLERS => $this->handlers
                ], $name);
            }
        }

        throw new Exception\RuntimeException(static::NO_ROUTES_EXCEPTION);
    }

    /**
     * @throws Exception\InvalidArgumentException
     */
    protected function addRouteFromDefinition(array $definition, string $uri): void
    {
        $this->scopePatterns = $this->patterns;

        if (isset($definition[static::PATTERNS])) {
            $routePatterns = $definition[static::PATTERNS];
            if (!is_array($routePatterns)) {
                throw new Exception\InvalidArgumentException(sprintf(static::INVALID_ROUTE_ARGUMENT_EXCEPTION, $uri));
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

            throw new Exception\InvalidArgumentException(sprintf(static::INVALID_ROUTE_ARGUMENT_EXCEPTION, $key));
        }
    }

    protected function addRouteItem(array $path, string $uri, array $parameters): void
    {
        Arrays::setValueAtPath($this->routeTree, $path, [
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
        if (Strings::isSurroundedBy($segment, '{', '}')) {
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
        if (isset($definition[static::URI])) {
            $uri = $this->uri($definition[static::URI], $uri);

            unset($definition[static::URI]);
        }

        // Route
        if (isset($definition[static::HANDLER])) {
            $namespace = rtrim($namespace, ':');
            $this->scopeName = $namespace;
            $this->handlers[$namespace] = $definition[static::HANDLER];

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
