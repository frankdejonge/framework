<?php

namespace Illuminate\Routing;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\RequestDispatcher as RequestDispatcherContract;
use Illuminate\Contracts\Routing\RouteBindingSubstitutor;
use Illuminate\Http\Request;

class RequestDispatcher implements RequestDispatcherContract, RouteBindingSubstitutor
{
    use HandlesMiddleware;
    use HandlesBindings;

    /**
     * @var \Illuminate\Routing\Router
     */
    private $router;

    /**
     * @param \Illuminate\Routing\Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param \Illuminate\Http\Request                  $request
     * @param \Illuminate\Contracts\Container\Container $container
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function dispatch(Request $request, Container $container)
    {
        return $this->router->dispatch($request);
    }
}