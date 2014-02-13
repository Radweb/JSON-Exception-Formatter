<?php namespace Radweb\JsonExceptionFormatter;

use Exception;
use Illuminate\Support\ServiceProvider;

class JsonExceptionFormatterServiceProvider extends ServiceProvider {

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
		$this->registerBindings();

		$this->wantsJson() and $this->registerErrorHandler();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

	protected function registerBindings()
	{
		$this->app->bind(
			'Radweb\JsonExceptionFormatter\FormatterInterface',
			'Radweb\JsonExceptionFormatter\BaseFormatter'
		);
	}

	/**
	 * @return bool
	 */
	protected function wantsJson()
	{
		return $this->app->runningInConsole() || $this->app['request']->ajax() || $this->app['request']->wantsJson();
	}

	protected function registerErrorHandler()
	{
		$this->app->error(
			function (Exception $exception)
			{
				$displayer = $this->app['Radweb\JsonExceptionFormatter\Displayer'];
				$debugMode = $this->app['config']['app.debug'];

				return $displayer->display($exception, $debugMode);
			}
		);
	}

}