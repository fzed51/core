<?php

namespace fzed51\Core;

abstract class SessionModule
{

    protected $session;
    
    protected $name;

    final public function registerModule(Session $session)
    {
        $this->session = $session;
        if (empty($this->name)) {
            throw new \Exception("Le nom du module n'est pas initialisé, nom inconnu");
        }
        $this->register();
    }

    abstract protected function register();

    final public function getName()
    {
        return $this->name;
    }

    final public function getMethodes()
    {
        $classReflect = new \ReflectionClass($this);
        $methodes = array_map(
            function ($methode) {
                return $methode->name;
            },
            $classReflect->getMethods(\ReflectionMethod::IS_PUBLIC)
        );
        return array_diff($methodes, ['register', 'registerModule', 'getName', 'getMethodes']);
    }
}
