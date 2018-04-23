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
 *       <title><?= $title ?></title>
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
 *           <?= $body ?>
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
 *   1. Making class Singleton - call static function to get its instance:
 *       $view = Template::getInstance();
 *
 *   2. Constructor pre defines some common variables to make it easier
 *      working with xdebug
 *
 *
 * @author Chad Minick
 * @link http://chadminick.com/articles/simple-php-template-engine.html
 *
 * @author szywo <szymon@wojdan.tk>
 *
 */
class Template
{
    private static $instance;
    private $vars  = array();

    /**
     * Private Constructor.
     *
     * Additionaly predefines some common variables for easier debugging.
     * That is no undefined variable warnings if something is ommited.
     */
    private function __construct()
    {
        $this->vars['page_title'] = "";
        $this->vars['page_base_path'] = "";
        $this->vars['page_css_file'] = "";
        $this->vars['page_menu_box'] = "";
        $this->vars['page_error_box'] = "";
        $this->vars['page_form_box'] = "";
        $this->vars['page_content_box'] = "";
        $this->vars['page_body'] = "";
    }

    /**
     * Returns stored or new instance of Template.
     *
     * @return object Returns instance of Template;
     */
    public static function getInstance()
    {
        if (self::$instance === null){
            self::$instance = new Template();
        }
        return self::$instance;
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
     * Magic function to set value of variable.
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
        extract($this->vars);
        ob_start();
        include($view_template_file);
        return ob_get_clean();
    }
}
