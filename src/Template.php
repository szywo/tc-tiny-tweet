<?php
namespace szywo\TinyTweet;

/**
 * Modified Chad Minick's Simple PHP Templating Engine.
 *
 * Simple PHP Templating Engine proposed by Chad Minick alowed simple, but
 * effective separation of application logic from its presentation.
 * How it works: Lets say $view is instance of Template, then:
 *
 * To use variable - for example page title:
 *
 *   1. in template file use:
 *       <title><?= $tpl_title ?></title>
 *
 *   2. in controller:
 *       $view->title = "My title";
 *
 *   3. in controler render page:
 *       echo $view-render("template_file_name.php");
 *
 *
 * To use additional sub-templates, for example for <body> content:
 *
 *   1. in template file use:
 *       <body>
 *           <?= $tpl_body ?>
 *       </body>
 *
 *   2. in controller (after setting all variables needed in <body>)
 *      render its content:
 *       $view->body = $view->render("body_subtemplate_file_name.php");
 *
 *   3. finaly, in controler, render page:
 *       echo $view-render("template_file_name.php");
 *
 * Modifications include:
 *   1. Constructor pre defines some common variables to make it easier
 *      working with xdebug
 *
 *   2. Modified render functin so that all extracted variables have
 *      VAR_PREFIX (default "tpl_") prefix added (to avoid overwriting
 *      existing variables). Underscore is enforced by the way prefixing
 *      works in extract() function but it is good because camelCase variable
 *      names are prefixed in very distinctive way: tpl_camelCase instead of
 *      tplcamelCase. Its one of rare cases where mixing naming conventions
 *      has positive outcome.
 *
 * @author Chad Minick
 * @link http://chadminick.com/articles/simple-php-template-engine.html
 *
 * @author szywo <szymon@wojdan.tk>
 *
 */
class Template
{
    const VAR_PREFIX = 'tpl';

    private $vars  = array();

    /**
     * Constructor.
     *
     * Predefines some common template variables for easier debugging.
     * That is no undefined variable warnings if something is ommited.
     */
    public function __construct()
    {
        $this->vars['title'] = "";
        $this->vars['basePath'] = "";
        $this->vars['cssFile'] = "";
        $this->vars['menuBox'] = "";
        $this->vars['infoBox'] = "";
        $this->vars['formBox'] = "";
        $this->vars['contentBox'] = "";
        $this->vars['body'] = "";
    }

    /**
     * Magic function to get value of variable.
     *
     * @param string $name Variable name
     * @return mixed Value of variable
     */
    public function __get($name)
    {
        return $this->vars[$name];
    }

    /**
     * Magic function to set value of variable.
     *
     * @param string $name Variable name
     * @param mixed $value Variable value
     * @return void
     */
    public function __set($name, $value)
    {
        if ($name == 'view_template_file') {
            throw new Exception("Cannot bind variable named 'view_template_file'");
        }
        $this->vars[$name] = $value;
    }

    /**
     * Extracts variables and renders template based on those.
     *
     * @param string $view_template_file Tamplate file to render
     * @return string The rendered tamplate with all variables substituded
     *                with their appropriate values.
     */
    public function render($view_template_file)
    {
        if (array_key_exists('view_template_file', $this->vars)) {
            throw new Exception("Cannot bind variable called 'view_template_file'");
        }
        extract($this->vars, EXTR_PREFIX_ALL, self::VAR_PREFIX);
        ob_start();
        include($view_template_file);
        return ob_get_clean();
    }
}
