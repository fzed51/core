<?php

namespace fzed51\Core;

/**
 * Description of Router
 *
 * @author fabien.sanchez
 */
class Routeur {

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

    /**
     * @var array Liste des routes
     */
    static private $_route = [];

    /**
     * @var string chemin de base pour la construction des liens
     */
    private static $_base_root = null;

    private function __construct($name, $path, $action) {
        $this->name = $name;
        $this->action = $action;
        $this->path = $path;
    }

    static public function setBaseUrl($base_root) {
        self::$_base_root = $base_root;
    }

    static public function set($name, $path, $action) {
        $newRoute = new self($name, $path, $action);
        self::$_route[$name] = $newRoute;
        //return $newRoute;
    }

    static public function dispatch($uri) {
        $uri = ltrim($uri, '/');

        $name = self::match($uri);

        if (!$name && ($uri == '' || $uri == 'home' || $uri == 'index.html') && isset(self::$_route['home'])) {
            self::$_route['home']->executeAction();
        } elseif (isset(self::$_route[$name])) {
            self::$_route[$name]->executeAction();
        } else {
            self::redirect(404, "Page introuvable ...");
        }
    }

    static private function match($uri) {
        foreach (self::$_route as $name => $route) {
            $matches = null;
            if (preg_match($route->pathToRegEx(), $uri, $matches)) {
                $_GET = array_merge($_GET, $matches);
                return $name;
            }
        }
        return false;
    }

    private function pathToRegEx() {
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

    private function executeAction() {
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

    static public function urlFor($name, array $options = [], array $attrib = []) {
        $base = self::getBaseUrl();
        if (!isset(self::$_route[$name])) {
            return self::concatPath($base, '/', '/');
        }
        /* $url = 'index.php?' . self::$_route[$name]->path;
          if (count($options) > 0) {
          foreach ($options as $option => $value) {
          $url = str_replace('{' . $option . '}', urldecode($value), $url);
          }
          }
         */
        $regEx = "/\\{([a-zA-Z0-9_.]+)(?:\\:[^\}]+)?}/";
        $parametres = [];
        $url = self::$_route[$name]->path;
        preg_match_all($regEx, $url, $parametres);
        foreach ($parametres[1] as $parametre) {
            $url = preg_replace($regEx, $options[$parametre], $url);
        }
        if (count($attrib) > 0) {
            $url .= '?';
            $start = true;
            foreach ($attrib as $key => $value) {
                if ($start) {
                    $start = false;
                } else {
                    $url .= '&';
                }
                $url .= $key . '=' . urldecode($value);
            }
        }
        return self::concatPath($base, $url, '/');
    }

    static public function getBaseUrl() {
        if (is_null(self::$_base_root)) {
            self::$_base_root = dirname($_SERVER['SCRIPT_NAME']);
        }
        return self::$_base_root . '/';
    }

    static private function concatPath($debut, $fin, $separator = '/') {
        $path = preg_replace("`[/\\\\]+(?:.[/\\\\]+)*`", $separator, $debut . $separator . $fin);
        return $path;
    }

    static private function redirect($code, $message = "") {
        http_response_code($code);
        if (isset(self::$_route[$code])) {
            self::$_route[$code]->executeAction();
        } else {
            echo $message;
        }
        die();
    }

    static public function getPath($name) {
        if (!isset(self::$_route[$name])) {
            throw new Exception("Ceste route n'existe pas !");
        }
        return self::concatPath(self::getBaseUrl(), self::$_route[$name]->path);
    }

}
