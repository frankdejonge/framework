<?php

namespace Illuminate\Routing\Dumped;

use Illuminate\Routing\Route;

class DumpedRoute extends Route
{
    public function __construct(array $methods, $uri, array $parameters, array $defaults = [], array $wheres = [], $domain = '')
    {
        $this->methods = $methods;
        $this->uri = $uri;
        $this->action = $parameters['_action'];
        unset($parameters['_action']);
        $this->parameters = $parameters;
        $this->defaults = $defaults;
        $this->wheres = $wheres;
    }

}