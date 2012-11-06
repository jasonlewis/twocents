<?php namespace TwoCents;

use Config;
use RuntimeException;

class Repository {

	/**
	 * Array of providers.
	 * 
	 * @var array
	 */
	public static $providers = array();

	/**
	 * Gets a provider or creates a new provider.
	 * 
	 * @param  string  $provider
	 * @return object
	 */
	public static function make($provider = null)
	{
		// If no provider is supplied we'll use the default provider.
		if (is_null($provider))
		{
			$provider = Config::get('twocents::twocents.repository.provider');
		}

		if ( ! isset(static::$providers[$provider]))
		{
			static::$providers[$provider] = static::factory($provider);
		}

		return static::$providers[$provider];
	}

	/**
	 * Create a new provider instance.
	 * 
	 * @param  string  $provider
	 * @return object
	 */
	protected static function factory($provider)
	{
		switch ($provider)
		{
			case 'github':
				return new Providers\GitHub;
		}

		throw new RuntimeException("Two Cents could not find provider [{$provider}]");
	}

	/**
	 * Divert any static calls to the default provider.
	 * 
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::make(), $method), $parameters);
	}
	
}