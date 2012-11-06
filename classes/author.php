<?php namespace TwoCents;

use Str;
use Config;

class Author extends Meta {

	/**
	 * Meta information contained within an author.
	 * 
	 * @var array
	 */
	protected $meta = array(
		'email',
		'homepage',
		'github',
		'twitter',
		'location'
	);

	/**
	 * Create a new Author instance.
	 * 
	 * @param  string  $name
	 * @param  object  $author
	 * @return void
	 */
	public function __construct($name, $author)
	{
		$this->name = str_replace(ARTICLE_EXTENSION, null, $name);

		$this->parse($author);
	}

	/**
	 * Overload the parse method adding extra functionality related to parsing an author.
	 * 
	 * @param  string  $article
	 * @return void
	 */
	public function parse($author)
	{
		$author = $this->parseMeta($author);

		$this->bio = Markdown::parse(trim($author));

		$this->slug = Str::slug($this->name);

		$this->gravatar = $this->gravatar();
	}

	/**
	 * Generate the authors Gravatar URL.
	 * 
	 * @return string
	 */
	protected function gravatar()
	{
		$config = Config::get('twocents::twocents.gravatar');

		if ( ! $config['enabled'])
		{
			return null;
		}

		// Hash the authors e-mail so we can request their Gravatar image.
		$hash = md5(strtolower(trim($this->email)));

		return 'http://www.gravatar.com/avatar/'.$hash.'?s='.$config['size'].'&d='.$config['default'];
	}

}