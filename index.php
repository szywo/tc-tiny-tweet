<?php
/**
 * TinyTweet app main controller.
 *
 * @package szywo\tinytweet
 * @author Szymon Wojdan
 * @link https://github.com/szywo
 */

/* Composer autoloader */
require __DIR__.'/vendor/autoload.php';

$view = new \chadminick\Template;
$request = new \szywo\tinytweet\Request(__FILE__);
$view->basePath = $request->getBasePath();

// routing
$view->bodyTemplate = "<h1>Helo World!</h1>";
echo $view->render("view/pageTemplate.html.php");
// routing end
