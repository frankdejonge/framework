<?php

namespace Illuminate\Routing\Dumped;

use ArrayObject;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\RequestDispatcher;
use Illuminate\Contracts\Routing\RouteBindingSubstitutor;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\HandlesBindings;
use Illuminate\Routing\HandlesMiddleware;
use Illuminate\Routing\Route;
use JsonSerializable;
use Symfony\Component\Routing\Matcher\UrlMatcher as SymfonyUrlMatcher;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

abstract class UrlMatcher extends SymfonyUrlMatcher implements RequestDispatcher, RouteBindingSubstitutor
{
    use HandlesBindings;
    use HandlesMiddleware;

    /**
     * Overwritten constructor.
     */
    public function __construct() { /* No constructor options needed. */}


    /**
     * @param \Illuminate\Http\Request                  $request
     * @param \Illuminate\Contracts\Container\Container $container
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function dispatch(Request $request, Container $container)
    {
        $this->context = $this->context->fromRequest($request);

        $match = $this->matchRequest($request);

        $route = new DumpedRoute([$request->method()], $this->context->getPathInfo(), $match);

        $container->instance(Route::class, $route);

        $route->setContainer($container);

        return $this->prepareResponse($request, $route->run());
    }

    /**
     * Create a response instance from the given value.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  mixed  $response
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function prepareResponse($request, $response)
    {
        if ($response instanceof PsrResponseInterface) {
            $response = (new HttpFoundationFactory)->createResponse($response);
        } elseif (! $response instanceof SymfonyResponse &&
            ($response instanceof Arrayable ||
                $response instanceof Jsonable ||
                $response instanceof ArrayObject ||
                $response instanceof JsonSerializable ||
                is_array($response))) {
            $response = new JsonResponse($response);
        } elseif (! $response instanceof SymfonyResponse) {
            $response = new Response($response);
        }

        return $response->prepare($request);
    }
}