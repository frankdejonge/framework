<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Dumped\UrlInformationDumper;
use Illuminate\Routing\Dumped\UrlMatcher;
use Illuminate\Routing\RouteCollection;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Routing\RouteCompiler;
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;
use Symfony\Component\Routing\RouteCollection as SymfonyRouteCollection;

class RouteDumpCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'route:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates dumped route files for faster url generation and request matching.';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new route command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->call('route:clear');

        $matcherPath = $this->laravel->getCachedRequestDispatcherPath();

        $routes = resolve('router')->getRoutes();

        $routeCollection = new SymfonyRouteCollection();

        foreach ($routes as $index => $route) {
            $routeCompiler = new RouteCompiler($route);

            $symfonyRoute = $routeCompiler->createSymfonyRoute();

            $name = $route->getName() ?: sprintf('route_at_index_%d', $index);

            $routeCollection->add($name, $symfonyRoute);
        }

        $dumper = new PhpMatcherDumper($routeCollection);

        $matcherCode = $dumper->dump([
            'base_class' => UrlMatcher::class,
            'class' => 'DumpedRequestDispatcher',
        ]);

        $this->files->put($matcherPath, $matcherCode);

        $urlInformationPath = $this->laravel->getCachedUrlInformationPath();

        $informationCode = (new UrlInformationDumper())->dump($routes);

        $this->files->put($urlInformationPath, $informationCode);

        $this->info('Routes dumped successfully!');
    }

    /**
     * Boot a fresh copy of the application and get the routes.
     *
     * @return \Illuminate\Routing\RouteCollection
     */
    protected function getFreshApplicationRoutes()
    {
        $app = require $this->laravel->bootstrapPath().'/app.php';

        $app->make(ConsoleKernelContract::class)->bootstrap();

        return $app['router']->getRoutes();
    }

    /**
     * Build the route cache file.
     *
     * @param  \Illuminate\Routing\RouteCollection  $routes
     * @return string
     */
    protected function buildRouteCacheFile(RouteCollection $routes)
    {
        $stub = $this->files->get(__DIR__.'/stubs/routes.stub');

        return str_replace('{{routes}}', base64_encode(serialize($routes)), $stub);
    }
}
