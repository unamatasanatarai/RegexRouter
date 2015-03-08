<meta charset='utf-8'><?php
// very bad testing!
require '../src/RegexRouter/RegexRouter.php';
use RegexRouter\RegexRouter;

$Router = new RegexRouter();
$Router->full_base_url = 'http://localhost/testingRegexRouter';

$Router->connect(array(
	'as'   => 'home',
	'path' => '/',
	'use'  => 'Home',
));

$Router->connect(array(
	'as'   => 'static-about-us-page',
	'path' => '/about-us',
	'use'  => 'Home@aboutUs',
	'custom_parameter' => 'was passed'
));

$Router->connect(array(
	'as'   => 'list-categories',
	'path' => 'categories',
	'use'  => 'Category@index',
));

$Router->connect(array(
	'as'   => 'show-category-entries-case-sensitive',
	'path' => 'Category/{SLUG}',
	'cs'   => true,
	'type' => 'GET',
	'use'  => 'Category@show'
));

$Router->connect(array(
	'as'   => 'show-category-entries',
	'path' => 'category/{SLUG}',
	'type' => 'GET',
	'use'  => 'Category@show'
));


$Router->connect(array(
	'as'   => 'random-one-slug',
	'path' => '{SLUG}',
	'type' => 'GET',
	'use'  => 'StaticPages@show'
));


$Router->connect(array(
	'as'   => 'random-infinite-after-slug',
	'path' => '{SLUG}/.*',
	'type' => 'GET',
	'use'  => 'StaticPages@show'
));


?>
<pre>
<?php
$tests = array(
	'http://localhost/testingRegexRouter' => array(
		'use' => 'Home',
		'as'  => 'home'
	),
	'http://localhost/testingRegexRouter/' => array(
		'use' => 'Home',
		'as'  => 'home'
	),
	'http://localhost/testingRegexRouter/about-us' => array(
		'use' => 'Home@aboutUs',
		'as'  => 'static-about-us-page'
	),
	'http://localhost/testingRegexRouter/about-us/' => array(
		'custom_parameter' => 'was passed',
		'as'  => 'static-about-us-page'
	),
	'http://localhost/testingRegexRouter/categories?failOnCustomParameter' => array(
		'custom_parameter' => 'was passed',
		'as'  => 'list-categories'
	),
	'http://localhost/testingRegexRouter/categories/' => array(
		'as'  => 'list-categories'
	),
	'http://localhost/testingRegexRouter/categories/?category=one&limit=threeąół' => array(
		'as'  => 'list-categories'
	),
	'http://localhost/testingRegexRouter/category/tyłó' => array(
		'as'  => 'show-category-entries'
	),
	'http://localhost/testingRegexRouter/category/tyłó/' => array(
		'as'  => 'show-category-entries'
	),
	'http://localhost/testingRegexRouter/category/tyłó?Ł=9&p=87' => array(
		'as'  => 'show-category-entries'
	),
	'http://localhost/testingRegexRouter/cAtegory/tyłó/?ok=ŁÓÆ' => array(
		'as'  => 'show-category-entries'
	),
	'http://localhost/testingRegexRouter/Category/tyłó/?ok=ŁÓÆ' => array(
		'as'  => 'show-category-entries-case-sensitive'
	),
	'http://localhost/testingRegexRouter/random_slug' => array(
		'as'  => 'random-one-slug'
	),
	'http://localhost/testingRegexRouter/random_slug/' => array(
		'as'  => 'random-one-slug'
	),
	'http://localhost/testingRegexRouter/random_slug/two/three/?fd' => array(
		'as'  => 'random-infinite-after-slug'
	),
);
$i = 1;
foreach ($tests as $route => $expecting) {
	echo "<div style=\"margin:10px 0 5px; padding:3px; border-top:1px solid #eee;border-bottom:1px solid #777\">$i\t$route</div>";
	$match = $Router->match($route);

	foreach($expecting as $variable => $value){
		$passed = (isset($match[$variable]) && $match[$variable] == $value)?'<span style="color:green">passed</span>':'<span style="color:red">failed!</span>';
		echo sprintf(
			"%s<br>\texpecting:\t<strong>%s</strong>=<em>%s</em><br>\treceived:\t<strong>%s</strong>=<em>%s</em><br>",
			$passed,
			$variable,
			$value,
			$variable,
			isset($match[$variable])
				? $match[$variable]
				: ''
		);
	}
	$i++;
}