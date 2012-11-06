<?php namespace TwoCents;

class Meta {

	/**
	 * Array of data.
	 * 
	 * @var array
	 */
	protected $data = array();

	/**
	 * Parse a files meta information.
	 * 
	 * @param  string  $contents
	 * @return string
	 */
	protected function parseMeta($contents)
	{
		// Match each of the meta information using the matcher method.
		foreach ($this->meta as $meta)
		{
			preg_match($this->matcher($meta), $contents, $matches);

			if (empty($matches))
			{
				continue;
			}

			list($search, $value) = $matches;

			$this->{$meta} = $value;

			if (($location = strpos($contents, $search)) !== false)
			{
				$contents = substr_replace($contents, null, $location, strlen($search));
			}

		}

		return $contents;
	}

	/**
	 * Returns the matching pattern regular expression.
	 * 
	 * @param  string  $meta
	 * @return string
	 */
	protected function matcher($meta)
	{
		return '/@'.$meta.'\s*(.*?)\n/';
	}

	/**
	 * Magic getter for meta information.
	 * 
	 * @param  string  $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	/**
	 * Magic setter for meta information.
	 * 
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

}