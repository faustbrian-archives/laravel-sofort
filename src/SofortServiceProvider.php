<?php


declare(strict_types=1);

/*
 * This file is part of Laravel Sofort.
 *
 * (c) Brian Faust <hello@brianfaust.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrianFaust\Sofort;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class SofortServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        $source = realpath(__DIR__.'/../config/sofort.php');

        $this->publishes([$source => config_path('sofort.php')]);

        $this->mergeConfigFrom($source, 'sofort');
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerFactory();
        $this->registerManager();
        $this->registerBindings();
    }

    /**
     * Register the factory class.
     */
    protected function registerFactory(): string
    {
        $this->app->singleton('sofort.factory', function () {
            return new SofortFactory();
        });

        $this->app->alias('sofort.factory', SofortFactory::class);
    }

    /**
     * Register the manager class.
     */
    protected function registerManager(): string
    {
        $this->app->singleton('sofort', function (Container $app) {
            $config = $app['config'];
            $factory = $app['sofort.factory'];

            return new SofortManager($config, $factory);
        });

        $this->app->alias('sofort', SofortManager::class);
    }

    /**
     * Register the bindings.
     */
    protected function registerBindings(): string
    {
        $this->app->bind('sofort.connection', function (Container $app) {
            $manager = $app['sofort'];

            return $manager->connection();
        });

        $this->app->alias('sofort.connection', Sofort::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides(): array
    {
        return [
            'sofort',
            'sofort.factory',
            'sofort.connection',
        ];
    }
}
