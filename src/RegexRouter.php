<?php
/**
 * @author Mike Dunn
 * @link https://github.com/moagrius/RegexRouter
 *
 * RegexRouter
 *
 *     PHP class to route with regular expressions. Extremely small. Follows
 *     every conceivable best-practice - SRP, SoC, DI, IoC, bfft...
 *
 * Usage
 *
 *     The only actual code is RegexRouter.php. index.php and the .htaccess
 *     file are just demoing usage. The 3 together in a TLD will function.
 *
 * Setup
 *
 *     1. make sure you're sending all requests to a front controller (either
 *        through apache conf directly or htaccess)
 *     2. include or require RegexRouter.php require_once 'RegexRouter.php';
 *     3. instantiate a new instance $router = new RegexRouter();
 *     4. add some routes $router->route('/^\/some\/pattern$/', <closure>);
 *     5. pass it either REQUEST_URI or any string for unit testing
 *        $router->execute($_SERVER['REQUEST_URI']);
 *
 *****************************************************************************
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2014
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */
namespace szywo\TinyTweet;

class RegexRouter
{
    private $routes = array();

    public function route($pattern, $callback)
    {
        $this->routes[$pattern] = $callback;
    }

    public function execute($uri)
    {
        foreach ($this->routes as $pattern => $callback) {
            if (preg_match($pattern, $uri, $params) === 1) {
                array_shift($params);
                return call_user_func_array($callback, array_values($params));
            }
        }
    }
}
