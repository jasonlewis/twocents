<?php

/*
|--------------------------------------------------------------------------
| Homepage
|--------------------------------------------------------------------------
|
| The Two Cents Homepage, shows a list of articles that have been posted.
|
*/

Route::get('(:bundle)', array('as' => 'twocents: home', function()
{
	$articles = TwoCents\Repository::articles();

	return View::of('twocents: theme')
			   ->nest('content', 'twocents::articles', compact('articles'))
			   ->with('title', 'Home');
}));

/*
|--------------------------------------------------------------------------
| Read an Article
|--------------------------------------------------------------------------
|
| Shows an individual article if it exists, otherwise it shows a pretty
| error page to the user.
|
*/

Route::get('(:bundle)/article/(:any)', array('as' => 'twocents: article', function($article)
{
	$theme = View::of('twocents: theme');

	if ( ! $article = TwoCents\Repository::article($article))
	{
		$theme->nest('content', 'twocents::oops')
			  ->with('title', 'Oops! Not found.');
	}
	else
	{
		$author = $article->author;

		$theme->nest('content', 'twocents::single', compact('article'))
			  ->nest('sidebar', 'twocents::author', compact('author'))
			  ->with('title', $article->title);
	}

	return $theme;		   
}));


/*
|--------------------------------------------------------------------------
| Show a Page
|--------------------------------------------------------------------------
|
| Anything that's not an article is considered a page. Pages can be nested
| a number of times.
|
*/

Route::get('(:bundle)/(:all)', array('as' => 'twocents: page', function($page)
{
	$theme = View::of('twocents: theme');

	if( ! $page = TwoCents\Repository::page($page))
	{
		$theme->nest('content', 'twocents::oops')
			  ->with('title', 'Oops! Not found.');
	}
	else
	{
		$theme->nest('content', 'twocents::page', compact('page'))
			  ->with('title', $page->title);
	}

	return $theme;
}));