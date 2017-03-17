<?php

namespace Illuminate\Routing\Middleware;

use Closure;
use Illuminate\Contracts\Routing\RouteBindingSubstitutor;

class SubstituteBindings
{
    /**
     * The router instance.
     *
     * @var \Illuminate\Contracts\Routing\RouteBindingSubstitutor
     */
    protected $bindingSubstitutor;

    /**
     * Create a new bindings substitutor.
     *
     * @param  \Illuminate\Contracts\Routing\RouteBindingSubstitutor $bindingSubstitutor
     * @return void
     */
    public function __construct(RouteBindingSubstitutor $bindingSubstitutor)
    {
        $this->bindingSubstitutor = $bindingSubstitutor;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->bindingSubstitutor->substituteBindings($route = $request->route());

        $this->bindingSubstitutor->substituteImplicitBindings($route);

        return $next($request);
    }
}
