<?php namespace TwoCents\Providers;

use Cache;
use Config;

abstract class Provider {

	/**
	 * Array of cURL requests queued.
	 * 
	 * @var array
	 */
	protected $queue = array();

	/**
	 * Array of cached items.
	 * 
	 * @var array
	 */
	protected $cached = array();

	/**
	 * Runtime cache configuration.
	 * 
	 * @var bool
	 */
	protected $cache = true;

	/**
	 * Get all articles.
	 * 
	 * @return array
	 */
	abstract public function articles();

	/**
	 * Get an individual article.
	 * 
	 * @param  string  $article
	 * @return array
	 */
	abstract public function article($article);

	/**
	 * Get an author.
	 * 
	 * @param  string  $author
	 * @return array
	 */
	abstract public function author($author);

	/**
	 * Classes extending the provider must have an errors method.
	 * 
	 * @param  string  $item
	 * @return mixed
	 */
	abstract protected function errors($item);

	/**
	 * Get an individual page.
	 * 
	 * @param  string  $page
	 * @return array
	 */
	public function page($page)
	{
		// Generally a page can be parsed the same as an article, however providers are free to adjust this
		// as they see fit.
		return $this->article($page);
	}

	/**
	 * Queue a cURL request.
	 * 
	 * @param  string  $method
	 * @return TwoCents\Provider
	 */
	protected function queue($method)
	{
		$curl = curl_init($this->api.$this->method($method));

		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		if ($username = Config::get('twocents::twocents.authentication.username') and $password = Config::get('twocents::twocents.authentication.password'))
		{
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $username.':'.$password);
		}

		$this->queue[$method] = $curl;

		return $this;
	}

	/**
	 * Runs the cURL request after adding another to the queue if supplied.
	 * 
	 * @param  string  $method
	 * @return object
	 */
	protected function fetch($method = null)
	{
		if ( ! is_null($method)) $this->queue($method);

		// If we have a cached copy of the queued requests we'll return that, otherwise we'll need
		// to run the request and cache it if we can.
		if ($this->cached())
		{
			$content = $this->cached();
		}
		else
		{
			if (empty($this->queue))
			{
				return array();
			}

			$ch = curl_multi_init();

			// Loop over each of the queued cURL requests and add the handle to the multi handler.
			foreach ($this->queue as $curl)
			{
				curl_multi_add_handle($ch, $curl);
			}

			do 
	    	{
	        	$execute = curl_multi_exec($ch, $active);
	    	}
	    	while ($execute == CURLM_CALL_MULTI_PERFORM or $active);

	    	// After the cURL requests have been executed we can again loop over the queued requests
	    	// and fetch the content for each one.
	    	$sha = null;

			$content = array();

	    	foreach ($this->queue as $method => $curl)
	    	{
	    		$sha .= $this->method($method);

	    		$content[] = $this->errors(json_decode(curl_multi_getcontent($curl)));

	    		curl_close($curl);
	    	}

	    	curl_multi_close($ch);

	    	$this->cache(sha1($sha), $content);
	    }

    	$this->queue = array();

    	$this->cache = true;

    	

		return $content;
	}

	/**
	 * Fetches the first result from a request.
	 * 
	 * @param  string  $method
	 * @return mixed
	 */
	protected function first($method = null)
	{
		$content = $this->fetch($method);

		return is_array($content) ? array_shift($content) : $content;
	}

	/**
	 * Gets the cached copy of the result.
	 * 
	 * @param  string  $sha
	 * @return string
	 */
	protected function cached($sha = null)
	{
		// If caching is not enabled then we'll just return false right here and not
		// go any further.
		if ( ! Config::get('twocents::twocents.caching.enabled') or ! $this->cache)
		{
			return false;
		}

		// If no sha hash is provided then we'll build our hash from the methods supplied
		// to our queue.
		if (is_null($sha))
		{
			foreach ($this->queue as $method => $curl)
			{
				$sha .= $this->method($method);
			}

			$sha = sha1($sha);
		}

		if (isset($this->cached[$sha]))
		{
			return $this->cached[$sha];
		}

		return $this->cached[$sha] = json_decode(Cache::get($sha, null));
	}

	/**
	 * Cache a value by its sha hash.
	 * 
	 * @param  string  $sha
	 * @param  string  $value
	 * @return void
	 */
	protected function cache($sha, $value)
	{
		if (Config::get('twocents::twocents.caching.enabled'))
	    {
			Cache::put($sha, json_encode($value), Config::get('twocents::twocents.caching.age'));
		}
	}

	/**
	 * Disable the caching for the next fetch request.
	 * 
	 * @return TwoCents\Provider
	 */
	protected function disableCache()
	{
		$this->cache = false;

		return $this;
	}

	/**
	 * Builds the API method.
	 * 
	 * @param  string  $method
	 * @return string
	 */
	protected function method($method)
	{
		$patterns = array(
			':repo' => Config::get('twocents::twocents.repository.name'),
			':revision' => 'master'
		);

		return str_replace(array_keys($patterns), array_values($patterns), $method);
	}

	/**
	 * Determine if an article is ignored.
	 * 
	 * @param  string  $name
	 * @return bool
	 */
	protected function isIgnored($name)
	{
		foreach (Config::get('twocents::twocents.ignored_articles') as $ignored)
		{
			$pattern = str_replace('*', '(.*)', $ignored).'\z';

			if ((bool) preg_match('#'.$pattern.'#', $name))
			{
				return true;
			}
		}

		return false;
	}

}