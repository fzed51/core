<?php

namespace fzed51\Core;

class Session
{

    /**
     * @var Session $instance est l'instance de la session
     */
    static private $instance;

    /**
     * @var array   $module est un tableau de module de session
     */
    static private $modules;

    /**
     * @var array   $registred_methodes est un tableau des méthodes autorisée
     */
    static private $registred_methodes;

    /**
     * register
     *
     * initilalise la session et ses modules
     */
    public static function redister()
    {
    }

    /**
     * addModule
     *
     * Enregistre un module de session. l'initialise si la session est déjà initialisée
     *
     * @param $module \fzed51\Core\SessionModule
     */
    public static function addModule(SessionModule $module)
    {
        $module_name = $module->getName();
        self::$module[$module_name] = $module;
        if (!is_null(self::$instance)) {
            $module->registerModule(self::$instance);
            $methodes = $module->getMethodes();
            foreach ($methodes as $methode) {
                self::$registred_methodes[$methode] = $module_name;
            }
        }
    }

    public static function __CallStatic($methode, $arguments)
    {
        if(isset(self::$registred_methodes[$methode])){
            $module_name = self::$registred_methodes[$methode];
            $module = self::$modules[$module_name];
            if(empty($arguments)){
                return call_user_func([$module, $methode]);
            } else {
                return call_user_func([$module, $methode], $arguments);
            }
        }
        throw new \Exception("Session : Méthode inconnue");        
    }

    /**
     * get
     *
     * lit une donnée dans la session
     */
    public static function get($offset, $default = null)
    {
        self::register();
        if(self::$instance->has($offset)){
            return self::$instance->read($offset);
        }
        return $default;
    }

    /**
     * set
     *
     * écrit une donnée dans la session
     */
    public static function set($offset, $value)
    {
        self::register();
        self::$instance->write($offset, $value);
    }
    
    /**
     * has
     *
     * indique si une donnée existe en session
     */
    public static function has($offset)
    {
        self::register();
        return self::$instance->has($offset);
    }

    // ----------------------------------------------------------------------

    protected function __construct()
    {
    }

    function __destruct()
    {
    }

    public function destroy()
    {
    }

    public function read($offset)
    {
    }

    public function write($offset, $value)
    {
    }

    public function has($offset)
    {
    }
}
