<?php

namespace SSLLabs\Laravel;


use Illuminate\Container\Container;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use SSLLabs\SSLLabs\Api;

class SsllabsServiceProvider extends ServiceProvider
{
    /**
     * Run service provider boot operations.
     *
     * @return void
     */
    public function boot()
    {
        $config = __DIR__.'/Config/config.php';
        $this->publishes([
            $config => config_path('ssllabs.php'),
        ], 'ssllabs');
        $this->mergeConfigFrom($config, 'ssllabs');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Bind the SSLLabs instance to the IoC
        $this->app->singleton('ssllabs', function (Container $app) {
            $config = $app->make('config')->get('ssllabs');
            // Verify configuration exists.
            if (is_null($config)) {
                $message = 'SSLLabs configuration could not be found. Try re-publishing using `php artisan vendor:publish --tag="ssllabs"`.';
                throw new ConfigurationMissingException($message);
            }
            return $this->newSSLLabs();
        });
        // Bind the SSLLabs contract to the SslLabs object
        // in the IoC for dependency injection.
        $this->app->singleton(SSLLabsInterface::class, 'ssllabs');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ssllabs'];
    }

    /**
     * Returns a new SSLLabs Api instance.
     *
     * @return SslLabs
     */
    protected function newSSLLabs()
    {
	\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
        return new Andyftw\SSLLabs\Api();
    }
}
