Using RegexRouter
=================

Creating Routes
---------------

Here is a basic setup to log to a file and to firephp on the DEBUG level:

```php
<?php
use RegexRouter\RegexRouter;

$Router = new RegexRouter();
$Router->full_base_url = 'http://localhost/testingRegexRouter';

$Router->connect(array(
	'as'   => 'home',
	'path' => '/',
	'use'  => 'Home'
));
$Router->connect(array(
	'as'   => 'static-about-us-page',
	'path' => '/about-us',
	'use'  => 'Home@aboutUs'
));
$Router->connect(array(
	'as'   => 'show-category',
	'path' => '{category:SLUG}/',
	'type' => 'GET',
	'use'  => 'Category@index'
));
$Router->connect(array(
	'as'   => 'show-category-entries',
	'path' => '{category:SLUG}/{post:SLUG}/',
	'use'  => 'Category@show'
));


var_dump($Router->match('http://localhost/testingRegexRouter/FirstCategory/Postname'));
```

Let's explain it. The first step is to define the Router and assign it our `$full_base_url`
It must NOT end in `/` slash.

Then we connect Named Routes. Available parameters are:
* as - Name to be used by ReverseRegexRouter. Can be any string, but try to keep it as: [a-zA-Z0-9_-]
* path - the paths to satisfy matching. Ending slash is not mandatory. `'path' => '/'  === 'path' => ''`
* type - The request type for later testing. options are GET|POST|PUT|DELETE|CUSTOM??
* use - Is what will be returned upon successful match

The matchin is done vertically, top-to-bottom


Protected arguments
-------------------

$Router->connect($arguments);
Anything you define in the `$arguments` will be carried over in the return of the match() function.
Except, the following will be overwritten/changed and should be considered as protected.

```php
$artuments = array(
	'as'       => 'route-name'
	'path'     => 'url-stub',
	'type'     => 'GET|POST',
	'prepared' => null,
	'cs'       => true|false,

	'use' => 'Controller@action', // Totally optional!
);
```
* *as* - name for the route. Useful and mandatory when using ReverseRegexRouter, which takes `as` as the first argument.
* *path* - what to match agains. So the remainder of $full_base_url[URL_STUB]
* *type* - will be converted into an array with any/all of the following array('GET', 'POST', 'PUT', 'DELETE'), by default `GET`
* *prepared* - is protected used internally. It holds a _pre compiled_ regext for the router.
* *cs* - CaseSensitive? By default, it is not. But for services like BitLy CaseSensitivity might be an issue.
* *use* - When matching is successful, you - the programmer - need to know what to run. I use `use` to point to an action in a controller. But you can use a file name, a number, anything. The variable `use` itself is not mandatory and feel free to use any name for it as you choose.
