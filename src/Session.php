<?php

namespace fzed51\Core;

use \fzed51\Core\SessionModule;

class Session {

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
    public static function register()
    {
        //if(is_null(self::$instance)){
        self::$instance = new self();
        //}
    }

    /**
     * addModule
     *
     * Enregistre un module de session. l'initialise si la session est déjà initialisée
     *
     * @param $module \fzed51\Core\SessionModule
     */
    //public static function addModule($module)
    public static function addModule(SessionModule $module)
    {
        $module_name = $module->getName();
        self::$modules[$module_name] = $module;
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
        if (isset(self::$registred_methodes[$methode])) {
            $module_name = self::$registred_methodes[$methode];
            $module = self::$modules[$module_name];
            if (empty($arguments)) {
                return call_user_func([$module, $methode]);
            } else {
                return call_user_func([$module, $methode], $arguments);
            }
        }
        throw new \Exception(self::errorMsg("Méthode inconnue"));
    }

    /**
     * get
     *
     * lit une donnée dans la session
     */
    public static function get($offset, $default = null)
    {
        self::register();
        if (self::$instance->is_set($offset)) {
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
        return self::$instance->is_set($offset);
    }

    private static function errorMsg($message)
    {
        return __CLASS__ . " : {$message}";
    }

    public static function listeMethodes()
    {
        return self::$registred_methodes;
    }

    // ----------------------------------------------------------------------

    protected function __construct()
    {
        if (!$this->isStart()) {
            if (!$this->start()) {
                throw new \Exception(self::errorMsg("Impossible de demarrer une session"));
            }
        }
    }

    function __destruct()
    {

    }

    public function destroy()
    {

    }

    public function read($offset)
    {
        if (is_array($offset)) {
            $offsets = $offset;
        } else {
            $offsets = explode('.', ((string) $offset));
        }
        return $this->read_reduce($_SESSION, $offsets);
    }

    private function read_reduce($in, array $offsets)
    {
        $offset = array_shift($offsets);
        if (empty($offsets)) {
            return $in[$offset];
        }
        return $this->read_reduce($in[$offset], $offsets);
    }

    public function write($offset, $value)
    {
        if (is_array($offset)) {
            $offsets = $offset;
        } else {
            $offsets = explode('.', ((string) $offset));
        }
        $this->write_reduce($_SESSION, $offsets, $value);
    }

    private function write_reduce(&$in, array $offsets, $value)
    {
        $offset = array_shift($offsets);
        if (empty($offsets)) {
            $in[$offset] = $value;
            return;
        }
        if (!isset($in[$offset]) || !is_array($in[$offset])) {
            $in[$offset] = [];
        }
        $this->write_reduce($in[$offset], $offsets, $value);
    }

    public function is_set($offset)
    {
        if (is_array($offset)) {
            $offsets = $offset;
        } else {
            $offsets = explode('.', ((string) $offset));
        }
        return $this->is_set_reduce($_SESSION, $offsets);
    }

    private function is_set_reduce($in, array $offsets)
    {
        if (empty($offsets)) {
            return true;
        }
        $offset = array_shift($offsets);
        if (isset($in[$offset])) {
            return $this->is_set_reduce($in[$offset], $offsets);
        }
        return false;
    }

    private function isEnviable()
    {
        $status = session_status();
        if ($status == PHP_SESSION_DISABLED) {
            throw new \Exception(self::errorMsg("Impossible d'utiliser les sessions, elles sont desactivee."));
        }
        $file = '';
        $line = 0;
        if ($status == PHP_SESSION_NONE && headers_sent($file, $line)) {
            throw new \Exception(self::errorMsg("Impossible d'utiliser les sessions, une entete a deja ete envoyee. {$file}({$line})"));
        }
        return true;
    }

    private function isStart()
    {
        $status = session_status();
        if (self::isEnviable()) {
            if ($status == PHP_SESSION_ACTIVE) {
                return true;
            } else {
                return false;
            }
        }
    }

    private function start()
    {
        return session_start();
    }

    private function close()
    {
        return session_write_close();
    }

}
