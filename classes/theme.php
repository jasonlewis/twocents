<?php namespace TwoCents;

use HTML;
use Config;
use RuntimeException;

class Theme {

	/**
	 * Links to a themes styles.
	 * 
	 * @param  string  $url
	 * @return string
	 */
	public static function style($url)
	{
		return static::asset($url, 'style');
	}

	/**
	 * Links to a themes scripts.
	 * 
	 * @param  string  $url
	 * @return string
	 */
	public static function script($url)
	{
		return static::asset($url, 'script');
	}

	/**
	 * Returns the HTML link to an asset.
	 * 
	 * @param  string  $url
	 * @param  string  $method
	 * @return string
	 */
	protected static function asset($url, $method)
	{
		$theme = Config::get('twocents::twocents.theme');

		return HTML::$method("bundles/twocents/{$theme}/{$url}");
	}

	/**
	 * Gets the path to the theme for the named view.
	 * 
	 * @return string
	 */
	public static function path()
	{
		$theme = Config::get('twocents::twocents.theme');

		if (file_exists($path = path('twocents') . 'themes' . DS . $theme . DS . 'theme' . EXT))
		{
			return "path: {$path}";
		}
		elseif (file_exists($path = path('twocents') . 'themes' . DS . $theme . DS . 'theme' . BLADE_EXT))
		{
			return "path: {$path}";
		}

		throw new RuntimeException("Two Cents could not find theme: {$theme}");
	}

}