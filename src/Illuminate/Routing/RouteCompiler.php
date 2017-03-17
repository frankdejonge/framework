<?php

namespace Illuminate\Routing;

use Symfony\Component\Routing\Route as SymfonyRoute;

class RouteCompiler
{
    /**
     * The route instance.
     *
     * @var \Illuminate\Routing\Route
     */
    protected $route;

    /**
     * Create a new Route compiler instance.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return void
     */
    public function __construct($route)
    {
        $this->route = $route;
    }

    /**
     * Compile the route.
     *
     * @return \Symfony\Component\Routing\CompiledRoute
     */
    public function compile()
    {
        return $this->createSymfonyRoute()->compile();
    }

    /**
     * Get the optional parameters for the route.
     *
     * @return array
     */
    protected function getOptionalParameters()
    {
        preg_match_all('/\{(\w+?)\?\}/', $this->route->uri(), $matches);

        $optionals = isset($matches[1]) ? array_fill_keys($matches[1], null) : [];

        $optionals['_action'] = $this->route->action;

        return $optionals;
    }

    /**
     * Get the information needed to generate a route.
     *
     * @return array
     */
    public function getRouteInformation()
    {
        return [
            'uri' => $this->route->uri,
            'methods' => $this->route->methods(),
            'defaults' => $this->route->defaults,
            'wheres' => $this->route->wheres,
            'parameters' => ['_action' => $this->route->action],
            'domain' => $this->route->domain() ?: ''
        ];
    }

    /**
     * @return SymfonyRoute
     */
    public function createSymfonyRoute()
    {
        $optionals = $this->getOptionalParameters();

        $uri = preg_replace('/\{(\w+?)\?\}/', '{$1}', $this->route->uri());

        return new SymfonyRoute($uri, $optionals, $this->route->wheres, [], $this->route->domain() ?: '');
    }
}
