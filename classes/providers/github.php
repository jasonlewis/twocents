<?php namespace TwoCents\Providers;

use Cache;
use Config;
use TwoCents\Author;
use TwoCents\Article;

class GitHub extends Provider {

	/**
	 * GitHub API URL
	 * 
	 * @var string
	 */
	public $api = 'https://api.github.com/';

	/**
	 * Retrieve an array of articles.
	 * 
	 * @return array
	 */
	public function articles()
	{
		$cached = $articles = $authors = array();

		// Fetch all the articles and authors from the repository. We need to get the contents of the directories so
		// that we can queue requests for each of the files within the directory.
		$this->queue('repos/:repo/contents/articles')
			 ->queue('repos/:repo/contents/authors');

		foreach ($this->fetch() as $repository)
		{
			foreach ($repository as $item)
			{
				if($item->type == 'file')
				{
					$this->queue('repos/:repo/contents/'.$item->path);

					// If the article has a matching directory within the articles directory we'll
					// load the contents of each of the files within that directory as well.
					foreach ($repository as $directory)
					{
						if ($directory->type == 'dir' and $directory->name == str_replace(ARTICLE_EXTENSION, null, $item->name))
						{
							$this->queue('repos/:repo/contents'.$directory->path);

							break;
						}
					}
				}
			}
		}

		// We should now have a queue of all the authors and articles that need to be fetched. If there's any
		// changes then they'll be loaded and recached.
		foreach ($this->fetch() as $key => $value)
		{
			// If the value is an object then we have an article or author. We need to add these to the appropriate arrays.
			if(is_object($value))
			{
				$name = str_replace(ARTICLE_EXTENSION, null, $value->name);

				if ($this->isIgnored($name))
				{
					continue;
				}

				if (starts_with($value->path, 'articles'))
				{
					$articles[$name] = new Article($value);
				}
				else
				{
					$authors[$name] = new Author($name, base64_decode($value->content));
				}
			}

			// If we don't have an object then this is a directory containing assets for a specific article. We need to
			// spin through each item and queue it so we can fetch the assets.
			else
			{
				foreach ($value as $item)
				{
					$this->queue('repos/:repo/contents/' . $item->path);
				}
			}
		}

		// Spin through each of the assets and add them to their respective articles.
		foreach ($this->fetch() as $item)
		{
			if (is_object($item))
			{
				$article = trim(str_replace(array($item->name, 'articles'), null, $item->path), '/');

				if (isset($articles[$article]))
				{
					$articles[$article]->registerAsset($item->name, base64_decode($item->content));
				}
			}
		}

		// Spin through each article and find the author for the article. We'll also assign the body of the article
		// and parse it so everything displays correctly.
		$sort = array();

		foreach ($articles as $article)
		{
			$article->body(base64_decode($article->raw->content))->parse();

			if (isset($authors[$article->author]))
			{
				$article->author($authors[$article->author]);
			}

			$sort[date('Y/m/d G i s', strtotime($article->date))] = $article;
		}

		krsort($sort);

		return $sort;
	}

	/**
	 * Retrieve a single article and all related information.
	 * 
	 * @param  string  $article
	 * @return object
	 */
	public function article($article)
	{
		if($article = $this->first('repos/:repo/contents/articles/' . $article . ARTICLE_EXTENSION))
		{
			$article = new Article($article);

			// Iterate over the asset directory of the article and if we have cached versions of the
			// asset we'll load that up instead.
			foreach ($this->first('repos/:repo/contents/articles/' . str_replace(ARTICLE_EXTENSION, null, $article->raw->name)) ?: array() as $asset)
			{
				if (is_object($asset))
				{
					$this->queue('repos/:repo/contents/' . $asset->path);
				}
			}

			// Spin through the assets we need to fetch and add them to the article.
			foreach ($this->fetch() as $asset)
			{
				if (is_object($asset))
				{
					$article->registerAsset($asset->name, base64_decode($asset->content));
				}
			}

			$article->body(base64_decode($article->raw->content))
					->parse();

			if ( ! is_null($article->author))
			{
				$article->author($this->author($article->author));
			}

			return $article;
		}
	}

	/**
	 * Checks if the request was successful.
	 * 
	 * @param  object  $item
	 * @return mixed
	 */
	protected function errors($item)
	{
		if (is_object($item))
		{
			if (property_exists($item, 'message') and $item->message == 'Not Found')
			{
				return array();
			}

			return $item;
		}
		elseif (is_array($item))
		{
			foreach ($item as $key => $value)
			{
				if (is_object($value) and property_exists($value, 'message') and $value->message == 'Not Found')
				{
					unset($item[$key]);
				}
			}

			return empty($item) ? false : $item;
		}

		return false;
	}

	/**
	 * Retrieve an author.
	 * 
	 * @return object
	 */
	public function author($author)
	{
		$file = $this->first('repos/:repo/contents/authors/' . $author . ARTICLE_EXTENSION);

		return new Author($author, base64_decode($file->content));
	}

}