<?php


namespace Illuminate\Contracts\Routing;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;

interface RequestDispatcher
{
    /**
     * @param \Illuminate\Http\Request   $request
     * @param \Illuminate\Contracts\Container\Container $container
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function dispatch(Request $request, Container $container);
}