<?php

namespace fzed51\Core;

abstract class SessionModule 
{

    protected $session;
    
    protected $name;

    final public function registerModule(Session $session)
    {
        $this->session = $session;
        if(empty($this->name)){
            throw new \Exception("Le nom du module n'est pas initialisé");
        }
        $this->register();
    }

    abstract protected function register();

    final public function getName()
    {
        return $this->name;
    }

    final public function getMethodes(){
        $class = get_class($this);
        $methodes = get_class_methods($class);
        return $methodes;
    }


}