<?php namespace TwoCents;

use Str;

class Article extends Meta {

	/**
	 * Raw article object.
	 * 
	 * @var object
	 */
	public $raw;

	/**
	 * Meta data contained within an article.
	 * 
	 * @var array
	 */
	protected $meta = array(
		'title',
		'author',
		'date',
		'intro'
	);

	/**
	 * Array of assets related to the article.
	 * 
	 * @var array
	 */
	protected $assets = array();

	/**
	 * Create a new Article instance with the raw article object.
	 * 
	 * @param  object  $article
	 * @return void
	 */
	public function __construct($article)
	{
		$this->raw = $article;
		$this->intro = 'yes';
	}

	/**
	 * Parse an articles body.
	 * 
	 * @return void
	 */
	public function parse()
	{
		$this->body = $this->parseMeta($this->body);

		// Look for code usages and replace them with the actual code samples contents
		// wrapped in our markdown code tags.
		preg_match_all('/<(.*)>/m', $this->body, $matches);

		list($search, $assets) = $matches;

		$replace = array();

		foreach ($assets as $key => $file)
		{
			if (str_contains('/', $file))
			{
				list($article, $file) = explode('/', $file);
			}

			if (isset($this->assets[$file]))
			{
				$replace[] = '~~~~'.PHP_EOL.$this->assets[$file].PHP_EOL.'~~~~';
			}
		}

		if ( ! empty($replace))
		{
			$this->body = str_replace($search, $replace, $this->body);
		}

		// Introductions can be disabled on a per article basis by using the @intro meta data.
		// If introductions are enabled then all the text before the first heading is considered
		// to be part of the intro.
		if (Str::lower($this->intro) == 'yes')
		{
			preg_match('/(.*?)<h(1|2|3|4|5)>/s', $this->body, $matches);

			if (empty($matches))
			{
				preg_match('/(.*?)@more/s', $this->body, $matches);

				$this->body = str_replace('@more', '', $this->body);
			}

			$this->intro = ( ! empty($matches) ? $matches[1] : $this->body);
		}
		else
		{
			$this->intro = $this->body;
		}

		$this->more = ($this->intro != $this->body);

		// Parse the body and intro of the article through the Markdown parser.
		$this->body = Markdown::parse(trim($this->body));

		$this->intro = Markdown::parse(trim($this->intro));

		// The article slug is simply a slugged version of the title. There should be no
		// duplicates.
		$this->slug = Str::slug($this->title);

		return $this;
	}

	/**
	 * Registers an asset from the assets directory to the article.
	 * 
	 * @param  string  $name
	 * @param  string  $contents
	 * @return object
	 */
	public function registerAsset($name, $contents)
	{
		$this->assets[$name] = $contents;

		return $this;
	}

	/**
	 * Associate an author with the article.
	 * 
	 * @param  object  $author
	 * @return object
	 */
	public function author($author)
	{
		$this->author = $author;

		return $this;
	}

	/**
	 * Sets the body of the article.
	 * 
	 * @param  string  $body
	 * @return object
	 */
	public function body($body)
	{
		$this->body = $body;

		return $this;
	}

	/**
	 * Return the body when casting object to string.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->body;
	}

}