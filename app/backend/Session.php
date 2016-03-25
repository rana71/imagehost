<?php namespace backend;

class Session {
    
    public static $objInstance = null;
    private static $nameSession = 'imagehost';
    
    private function __construct($strSessionName)
    {
        if (false === isset($_SESSION))
        {
            if (empty($strSessionName)) {
                $strSessionName = self::$nameSession;
            }
            session_name($strSessionName);
            session_start();
        }
    }

    public static function getInstance($strSessionName = '') {
        if (self::$objInstance === null) {
            self::$objInstance = new Session($strSessionName);
        }
        return self::$objInstance;
    }
    
    public function setValue($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function getValue($name, $mulDefaultValue = '')
    {
        if (isset($_SESSION[$name]))
            return $_SESSION[$name];
        return $mulDefaultValue;
    }
    
    public function get() {
        return $_SESSION;
    }

    public function setDelete($name)
    {
        if (isset($_SESSION[$name]))
            unset($_SESSION[$name]);
    }
    
    

    public static function destroy()
    {
        if (isset($_SESSION))
        {
            $_SESSION = array();
            unset($_SESSION);
            session_unset();
            session_destroy();
        }
    }
}