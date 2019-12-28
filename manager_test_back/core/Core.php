<?php
namespace core;

defined('CORE_PATH') or define('CORE_PATH', __DIR__);

class Core
{
    // configuration
    protected $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    // run the app
    public function run()
    {
        spl_autoload_register(array($this, 'loadClass'));
        $this->removeMagicQuotes();
        $this->setDbConfig();
        $this->route();
    }

    // router
    public function route()
    {
        $pathInfo = explode('/',$_SERVER['PATH_INFO']);

        $controllerName = $pathInfo[1];
        $controller = 'app\\controllers\\'. $controllerName . 'Controller';


        $method = strtolower($_SERVER['REQUEST_METHOD']);
        // check if the method is allowed
        if (!in_array($method, $this->config['method'])) {
            header("HTTP/1.1" . " " . "403" . " " . "Forbidden");
            echo "Cannot {$_SERVER['REQUEST_METHOD']} {$_SERVER['PATH_INFO']}";
            exit();
        }
        // check if the class exists
        if (!class_exists($controller)||!method_exists($controller, $method)) {
            header("HTTP/1.1" . " " . "404" . " " . "Not Found");
            echo "Cannot {$_SERVER['REQUEST_METHOD']} {$_SERVER['PATH_INFO']}";
            exit();
        }
        $pathInfo = array_slice($pathInfo, 2);
        $param = $pathInfo ? $pathInfo : array();

        $dispatch = new $controller($controllerName, $method);

        call_user_func_array(array($dispatch, $method), $param);
    }

    // filter
    public function stripSlashesDeep($value)
    {
        $value = is_array($value) ? array_map(array($this, 'stripSlashesDeep'), $value) : stripslashes($value);
        return $value;
    }

    // filter
    public function removeMagicQuotes()
    {
        if (get_magic_quotes_gpc()) {
            $_GET = isset($_GET) ? $this->stripSlashesDeep($_GET ) : '';
            $_POST = isset($_POST) ? $this->stripSlashesDeep($_POST ) : '';
            $_COOKIE = isset($_COOKIE) ? $this->stripSlashesDeep($_COOKIE) : '';
            $_SESSION = isset($_SESSION) ? $this->stripSlashesDeep($_SESSION) : '';
        }
    }


    // set database
    public function setDbConfig()
    {
        if ($this->config['db']) {
            define('DB_HOST', $this->config['db']['host']);
            define('DB_NAME', $this->config['db']['dbname']);
            define('DB_USER', $this->config['db']['username']);
            define('DB_PASS', $this->config['db']['password']);
        }
    }

    // load class
    public function loadClass($className)
    {
        $classMap = $this->classMap();

        if (isset($classMap[$className])) {
            // include core files
            $file = $classMap[$className];
        } elseif (strpos($className, '\\') !== false) {
            // include app files
            $file = APP_PATH . str_replace('\\', '/', $className) . '.php';
            if (!is_file($file)) {
                return;
            }
        } else {
            return;
        }

        include $file;
    }

    // classmap
    protected function classMap()
    {
        return [
            'core\base\Controller' => CORE_PATH . '/base/Controller.php',
            'core\db\Db' => CORE_PATH . '/db/Db.php',
        ];
    }
}