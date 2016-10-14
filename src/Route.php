<?php

namespace fzed51\Core;

/**
 * Description of Router
 *
 * @author fabien.sanchez
 */
class Route {

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

    function __construct($name, $path, $action) {
        $this->name = $name;
        $this->action = $action;
        $this->path = $path;
    }

    function pathToRegEx() {
        $rules = $this->rules;
        $fnReplace = function($matches) use($rules) {
            $re = '/(^|[^\\\\])(\()([^?])/';
            $subPattern = '[A-Za-z0-9._-]+';
            if (isset($rules[$matches[1]])) {
                $subPattern = preg_replace($re, '$1$2?:$3', $matches[2]);
            }
            return '(?<' . $matches[1] . '>' . $subPattern . ')';
        };
        $patterns = "/\{([^}\\\/]+)\}/";
        $path = '`^' . preg_replace_callback($patterns, $fnReplace, $this->path) . '$`';
        return $path;
    }

    public function setRules(array $rules) {
        $this->rules = $rules;
    }

    function executeAction() {
        $matches = [];
        $action = $this->action;
        if (is_callable($action)) {
            call_user_func($action);
            return;
        }
        if (is_string($action) && preg_match('/^(\w+)@([\w\\\\]+)$/', $action, $matches)) {
            $nom_methode = $matches[1];
            $nom_controleur = $matches[2];
            if (class_exists($nom_controleur)) {
                $methodes = get_class_methods($nom_controleur);
                if (array_search($nom_methode, $methodes) !== false) {
                    $controleur = new $nom_controleur();
                    call_user_func([$controleur, $nom_methode]);
                    return;
                }
            }
        }
        self::redirect(500, "Erreur d'executin de la page {$this->name}");
    }

    public function getPath() {
        return $this->path;
    }

}
