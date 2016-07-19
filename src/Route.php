<?php

namespace fzed51\Core;

/**
 * Description of Router
 *
 * @author fabien.sanchez
 */
class Route
{

    static private $_route = [];
    private static $_base_root = null;

    static public
            function setBaseUrl($base_root)
    {
        self::$_base_root = $base_root;
    }

    static public
            function set($name, $path, $action)
    {
        $newRoute = new \stdClass();
        $newRoute->name = $name;
        $newRoute->path = $path;
        $newRoute->action = $action;
        self::$_route[$name] = $newRoute;
    }

    static public
            function dispatch($uri)
    {
        $uri = ltrim($uri, '/');

        $name = self::match($uri);

        if (!$name && ($uri == '' || $uri == 'home' || $uri == 'index.html') && isset(self::$_route['home'])) {
            self::executeAction(self::$_route['home']);
        } elseif (isset(self::$_route[$name])) {
            self::executeAction(self::$_route[$name]);
        } else {
            self::redirect(404, "Page introuvable ...");
        }
    }

    static private
            function match($uri)
    {
        foreach (self::$_route as $name => $route) {
            $regex = self::pathToRegEx($route->path);
            if (preg_match($regex, $uri, $matches)) {
                $_GET = array_merge($_GET, $matches);
                return $name;
            }
        }
        return false;
    }

    static private
            function pathToRegEx($path)
    {
        $fnReplace = function ($matches) {
            if (array_search($matches[0], ['.', '\\', '+', '*', '?', '[', '^', ']', '$', '(', ')', '{', '}', '=', '!', '<', '>', '|', ':', '-', '`'])) {
                return '\\' . $matches[0];
            } else {
                $subPattern = '[A-Za-z0-9._-]+';
                if (isset($matches[2])) {
                    $subPattern = $matches[2];
                }
                return '(?<' . $matches[1] . '>' . $subPattern . ')';
            }
            return '';
        };
        $patterns = [
            "/(?:{([^}:]+)(?:\\:([^}]*))?})|\\.|\\\\|\\+|\\*|\\?|\\[|\\^|\\]|\\$|\\(|\\)|\\{|\\}|\\=|\\!|\\<|\\>|\\||\\:|\\-|\\`/"
        ];
        $path = '`^' . preg_replace_callback($patterns, $fnReplace, $path) . '$`';
        return $path;
    }

    static private
            function executeAction($route)
    {
        $matches = [];
        $action = $route->action;
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
        self::redirect(500, "Erreur d'executin de la page {$route->name}");
    }

    static public
            function urlFor($name, array $options = [], array $attrib = [])
    {
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
                    $url.='&';
                }
                $url .= $key . '=' . urldecode($value);
            }
        }
        return self::concatPath($base, $url, '/');
    }

    static public
            function getBaseUrl()
    {
        if (is_null(self::$_base_root)) {
            self::$_base_root = dirname($_SERVER['SCRIPT_NAME']);
        }
        return self::$_base_root . '/';
    }

    static private
            function concatPath($debut, $fin, $separator = '/')
    {
        $path = preg_replace("`[/\\\\]+(?:.[/\\\\]+)*`", $separator, $debut . $separator . $fin);
        return $path;
    }

    static private
            function redirect($code, $message = "")
    {
        http_response_code($code);
        if (isset(self::$_route[$code])) {
            self::executeAction(self::$_route[$code]);
        } else {
            echo $message;
        }
        die();
    }

    static public
            function getPath($name)
    {
        if (!isset(self::$_route[$name])) {
            throw new Exception("Ceste route n'existe pas !");
        }
        return self::concatPath(self::getBaseUrl(), self::$_route[$name]->path);
    }

}
