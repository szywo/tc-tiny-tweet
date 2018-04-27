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
 *   3. Added automatic filtering (escaping) mechanism for variables.
 *      It is so that Template class can adhere to automatic filtering based
 *      on destination principle (that is, it we keep variables as raw values
 *      as long as possible and use automated filtering based on intended
 *      destination)
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
    private $rawVars = array();
    private $defEncoding;
    private $encodings = array();

    /**
     * Constructor.
     *
     * Predefines some common template variables for easier debugging.
     * That is no undefined variable warnings if something is ommited.
     */
    public function __construct()
    {
        $this->defEncoding = ENT_COMPAT|ENT_HTML5;
        // default variables
        $this->vars['title'] = "";
        $this->vars['basePath'] = "";
        $this->vars['cssFile'] = "";
        $this->vars['menuBoxTemplate'] = "";
        $this->vars['infoBoxTemplate'] = "";
        $this->vars['formBoxTemplate'] = "";
        $this->vars['contentBoxTemplate'] = "";
        $this->vars['bodyTemplate'] = "";
        // set those variables as raw
        $this->setRaw([
            'menuBoxTemplate',
            'infoBoxTemplate',
            'formBoxTemplate',
            'contentBoxTemplate',
            'bodyTemplate'
        ]);
    }

    /**
     * Magic function to get value of variable.
     *
     * @param string $name Variable name
     * @return mixed Value of variable
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->rawVars)) {
            return $this->rawVars[$name];
        }
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
        // check if variable was already set as raw value
        if (array_key_exists($name, $this->rawVars)) {
            $this->rawVars[$name] = $value;
        } else {
            $this->vars[$name] = $value;
        }
    }

    /**
     * Sets variable as raw value so there will be no encoding
     *
     * @param string|string[] $name Variable name or array of variable names
     * @return void
     */
    public function setRaw($name)
    {
        $names = array();
        if (! is_array($name)) {
            $names[] = $name;
        } else {
            $names = $name;
        }
        foreach ($names as $name) {
            if (array_key_exists($name, $this->vars)) {
                $this->rawVars[$name] = $this->vars[$name];
                unset($this->vars[$name]);
            } else {
                // sets raw format for variable before it is "initialised"
                $this->rawVars[$name] = '';
            }
        }
    }

    /**
     * Sets variable as value to be encoded
     *
     * @param string|string[] $name Variable name
     * @return void
     */
    public function unSetRaw($name)
    {
        $names = array();
        if (! is_array($name)) {
            $names[] = $name;
        } else {
            $names = $name;
        }
        foreach ($names as $name) {
            if (array_key_exists($name, $this->rawVars)) {
                $this->vars[$name] = $this->rawVars[$name];
                unset($this->rawVars[$name]);
            } // not "initialised" variables are encoded by default
        }
    }

    /**
     * Sets encoding used to process value
     *
     * @param string|string[] $name Variable name
     * @param string $encoding htmlspecialhars() encoding type
     * @return void
     */
    public function setEncoding($name, $encoding)
    {
        $names = array();
        if (! is_array($name)) {
            $names[] = $name;
        } else {
            $names = $name;
        }
        foreach ($names as $name) {
            $this->encodings[$name];
        }
    }

    /**
     * Changes default encoding (ENT_COMPAT|ENT_HTML5)
     *
     * @param string $encoding htmlspecialhars() encoding type
     * @return void
     */
    public function setDefaultEncoding($encoding)
    {
        $this->defEncoding = $encoding;
    }

    /**
     * Recursively encodes variables in arrays
     *
     * @param mixed $var Variable to encode
     * @param mixed $encoding Encoding type to use
     * @return string|array Encoded string or array
     */
    private function encode($var, $encoding) {
        $encodedVar = null;
        if (is_object($var) || is_scalar($var)) {
            $encodedVar = htmlspecialchars((string) $var, $encoding);
        }
        if (is_array($var)) {
            foreach ($var as $key => $val) {
                $encodedVar[$key] = $this->encode($val, $encoding);
            }
        }
        return $encodedVar;
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
        $encoded = array();
        foreach ($this->vars as $name => $var) {
            $encoding = $this->encodings[$name]??$this->defEncoding;
            $encoded[$name] = $this->encode($var, $encoding);
        }
        extract($encoded, EXTR_PREFIX_ALL, self::VAR_PREFIX);
        extract($this->rawVars, EXTR_PREFIX_ALL, self::VAR_PREFIX);
        ob_start();
        include($view_template_file);
        return ob_get_clean();
    }
}
