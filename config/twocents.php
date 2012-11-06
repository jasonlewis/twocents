<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Repository Details
	|--------------------------------------------------------------------------
	|
	| This is the repository where your articles are kept. TwoCents supports
	| the following providers: github
	|
	| The name of your repository is the full name including your username.
	| This can generally be taken from the URL of your repository.
	|
	*/

	'repository' => array(
		'provider' => 'github',
		'name' => ''
	),

	/*
	|--------------------------------------------------------------------------
	| Basic Authentication
	|--------------------------------------------------------------------------
	|
	| Two Cents operates best when you provide some basic authentication for
	| your provider. Some providers may impose an API rate limit, this limit is
	| generally higher if some kind of authentication is given.
	|
	*/

	'authentication' => array(
		'username' => '',
		'password' => ''
	),

	/*
	|--------------------------------------------------------------------------
	| Article Extension
	|--------------------------------------------------------------------------
	|
	| This is the extension that your articles will be saved in. Articles are
	| parsed with Markdown and so should be saved with an 'md' or 'markdown'
	| extension.
	|
	*/
	'article_extension' => 'md',

	/*
	|--------------------------------------------------------------------------
	| Theme
	|--------------------------------------------------------------------------
	|
	| This is the theme that your blog is using. You can create your own themes
	| and change it in here.
	|
	*/

	'theme' => 'basic',

	/*
	|--------------------------------------------------------------------------
	| Caching
	|--------------------------------------------------------------------------
	|
	| Caching articles is very important to reduce the load time of your blog.
	| An article should only be merged into your repository when you're sure it
	| meets quality standards, so enabling caching and setting the age to
	| an hour or more should be no problem.
	|
	| The age is in minutes. Somewhere around 60 or more should be fine.
	|
	*/

	'caching' => array(
		'enabled' => true,
		'age' => 120
	),

	/*
	|--------------------------------------------------------------------------
	| Gravatar
	|--------------------------------------------------------------------------
	|
	| Configure the authors Gravatar. The default, if not one of Gravatars
	| supplied options, must be passed through urlencode() before pasting it
	| in here.
	|
	*/

	'gravatar' => array(
		'enabled' => true,
		'size' => 100,
		'default' => 'mm'
	),

	/*
	|--------------------------------------------------------------------------
	| Disqus Comments
	|--------------------------------------------------------------------------
	|
	| Disqus comments can be enabled for your blog. You can get the shortname
	| from your Disqus administration panel.
	|
	*/

	'disqus' => array(
		'enabled' => false,
		'shortname' => ''
	),

	/*
	|--------------------------------------------------------------------------
	| Ignored Articles
	|--------------------------------------------------------------------------
	|
	| This is an array of ignored articles that are in your repository. You may
	| want to ignore a number of articles that provide information about
	| contributing or that are used on a different page.
	|
	*/

	'ignored_articles' => array(
		''
	)
	
);