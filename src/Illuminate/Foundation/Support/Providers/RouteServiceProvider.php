<?php

namespace Illuminate\Foundation\Support\Providers;

use Illuminate\Routing\RequestDispatcher;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\UrlGenerator;
use Symfony\Component\Routing\RequestContext;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the application.
     *
     * @var string|null
     */
    protected $namespace;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setRootControllerNamespace();
    }

    /**
     * Set the root controller namespace for the application.
     *
     * @return void
     */
    protected function setRootControllerNamespace()
    {
        $this->app->resolving(UrlGenerator::class, function (UrlGenerator $urlGenerator) {
            $urlGenerator->setRootControllerNamespace($this->namespace);
        });
    }

    /**
     * Load the application routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        if ($this->app->routesAreCached()) {
            $this->loadCachedRoutes();
        } else {
            $this->loadDefinedRoutes();

            $this->app['router']->getRoutes()->refreshNameLookups();
        }
    }

    /**
     * Load the cached routes for the application.
     *
     * @return void
     */
    protected function loadCachedRoutes()
    {
        require $this->app->getCachedRoutesPath();
    }

    /**
     * Load the routes defined by the application.
     *
     * @return void
     */
    protected function loadDefinedRoutes()
    {
        if (method_exists($this, 'map')) {
            $this->app->call([$this, 'map']);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRouter();

        $this->registerRequestDispatcher();

        $this->registerUrlInformation();
    }

    /**
     * Register the request dispatcher.
     *
     * @return void
     */
    protected function registerUrlInformation()
    {
        $this->app->singleton('url.information', function ($app) {
            if ($this->app->urlInformationIsCached()) {
                require_once $this->app->getCachedUrlInformationPath();

                return new \DumpedUrlInformation();
            } else {
                return $app['router']->getRoutes();
            }
        });
    }

    /**
     * Register the request dispatcher.
     *
     * @return void
     */
    protected function registerRequestDispatcher()
    {
        $this->app->singleton('request.dispatcher', function ($app) {
            if ($this->app->requestDispatcherIsCached()) {
                require_once $this->app->getCachedRequestDispatcherPath();

                return new \DumpedRequestDispatcher(new RequestContext());
            } else {
                return new RequestDispatcher($app['router']);
            }
        });
    }

    /**
     * Register the router instance.
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });

        $this->app->resolving('router', function () {
            $this->loadRoutes();
        });
    }

    /**
     * Pass dynamic methods onto the router instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(
            [$this->app->make('router'), $method], $parameters
        );
    }
}
