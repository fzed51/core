<?php

namespace fzed51\Core;

/**
 * Description of Router
 *
 * @author fabien.sanchez
 */
class Route
{

    /**
     * @var string Chemin/regex de la route
     */
    private $path;

    /**
     * @var callable/string action de la route
     */
    private $action;

    /**
     * @var string nom de la route
     */
    private $name;

    /**
     * @var array règle de validation des paramètres
     */
    private $rules = [];

    public function __construct($name, $path, $action)
    {
        $this->name = $name;
        $this->action = $action;
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function pathToRegEx()
    {
        $rules = $this->rules;
        $fnReplace = function ($matches) use ($rules) {
            $subPattern = '[A-Za-z0-9._-]+';
            // Recherche dans la règle des Capturing group pour les remplacer
            // par des Non-capturing Group
            $regex = '/(^|[^\\\\])(\()([^?])/';
            if (isset($rules[$matches[1]])) {
                $subPattern = preg_replace($regex, '$1$2?:$3', $rules[$matches[1]]);
            }
            return '(?<' . $matches[1] . '>' . $subPattern . ')';
        };
        $patterns = "/\{([^}\/]+)\}/";
        $path = '`^' . preg_replace_callback($patterns, $fnReplace, $this->path) . '$`';
        return $path;
    }

    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    public function executeAction()
    {
        $matches = [];
        $action = $this->action;
        if (is_callable($action)) {
            return call_user_func($action);
        }
        if (is_string($action) && preg_match('/^(\w+)@([\w\\\\]+)$/', $action, $matches)) {
            $nom_methode = $matches[1];
            $nom_controleur = $matches[2];
            if (class_exists($nom_controleur)) {
                $methodes = get_class_methods($nom_controleur);
                if (array_search($nom_methode, $methodes) !== false) {
                    $controleur = new $nom_controleur();
                    return call_user_func([$controleur, $nom_methode]);
                }
            }
        }
        throw new \Exception("Impossible d'executer l'action de la route {$this->name}");
    }
}
