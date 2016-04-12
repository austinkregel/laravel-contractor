<?php namespace Kregel\Contractor;

use Illuminate\Support\ServiceProvider;

class ContractorServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Preform booting of services...
	 */
	public function boot()
	{
		if (!$this->app->routesAreCached()) {
			$this->app->router->group(['namespace' => 'Kregel\Contractor\Http\Controllers'], function ($router) {
				require __DIR__.'/Http/routes.php';
			});
		}
		$this->loadViewsFrom(__DIR__.'/../resources/views', 'contractor');
		$this->publishes([
			__DIR__.'/../resources/views' => base_path('resources/views/vendor/contractor'),
		], 'views');
		$this->publishes([
			__DIR__.'/../config/config.php' => config_path('kregel/contractor.php'),
		], 'config');
	}
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [];
	}

}
