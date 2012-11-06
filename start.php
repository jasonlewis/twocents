<?php

/*
|--------------------------------------------------------------------------
| Important Bits and Pieces
|--------------------------------------------------------------------------
|
| Set some important bits and pieces here. We'll define a path to the base
| directory of Two Cents and define the article extension constant.
|
*/

set_path('twocents', __DIR__.DS);

define('ARTICLE_EXTENSION', '.'.Config::get('twocents::twocents.article_extension', 'md'));

/*
|--------------------------------------------------------------------------
| Autoload Two Cents Namespace
|--------------------------------------------------------------------------
|
| Two Cents is within it's own lovely namespace. This keeps things nice
| and tidy.
|
*/

Autoloader::namespaces(array(
	'TwoCents' => path('twocents').'classes'
));

/*
|--------------------------------------------------------------------------
| Name Theme View
|--------------------------------------------------------------------------
|
| To make things easier will name our theme's view so that routes can
| easily call it.
|
*/

View::name(TwoCents\Theme::path(), 'twocents: theme');