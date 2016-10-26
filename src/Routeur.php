<?php

namespace fzed51\Core;

use fzed51\Core\Route;

/**
 * Description of Router
 *
 * @author fabien.sanchez
 */
class Routeur
{

    /**
     * @var array Liste des routes
     */
    static private $route = [];

    /**
     * @var string chemin de base pour la construction des liens
     */
    private static $base_root = null;

    private function __construct()
    {
    }

    public static function clear()
    {
        self::$base_root = null;
        self::$route = [];
    }
 
    public static function setBaseUrl($base_root)
    {
        self::$base_root = rtrim(str_replace("\\", "/", $base_root), "/");
    }

    public static function getBaseUrl()
    {
        if (is_null(self::$base_root)) {
            self::setBaseUrl(dirname($_SERVER['SCRIPT_NAME']));
        }
        return self::$base_root . '/';
    }

    public static function set($name, $path, $action)
    {
        $newRoute = new Route($name, $path, $action);
        self::$route[$name] = $newRoute;
        return $newRoute;
    }

    public static function dispatch($uri)
    {
        $uri = ltrim($uri, '/');

        $name = self::match($uri);

        try {
            if (!$name && ($uri == '' || $uri == 'home' || $uri == 'index.html') && isset(self::$route['home'])) {
                self::$route['home']->executeAction();
            } elseif (isset(self::$route[$name])) {
                self::$route[$name]->executeAction();
            } else {
                self::redirect(404, "Page introuvable ...");
            }
        } catch (\Exception $e) {
            self::redirect(500, "Erreur serveur ...");
        }
    }

    private static function match($uri)
    {
        foreach (self::$route as $name => $route) {
            $matches = null;
            if (preg_match($route->pathToRegEx(), $uri, $matches)) {
                $_GET = array_merge($_GET, $matches);
                return $name;
            }
        }
        return false;
    }

    public static function urlFor($name, array $options = [], array $attrib = [])
    {
        $base = self::getBaseUrl();
        if (!isset(self::$route[$name])) {
            return self::concatPath($base, '/', '/');
        }
        /* $url = 'index.php?' . self::$route[$name]->path;
          if (count($options) > 0) {
          foreach ($options as $option => $value) {
          $url = str_replace('{' . $option . '}', urldecode($value), $url);
          }
          }
         */
        $regEx = "/\\{([a-zA-Z0-9_.]+)\\}/";
        $parametres = [];
        $url = self::$route[$name]->path;
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

    private static function concatPath($debut, $fin, $separator = '/')
    {
        $path = preg_replace("`[/\\\\]+(?:.[/\\\\]+)*`", $separator, $debut . $separator . $fin);
        return $path;
    }

    private static function redirect($code, $message = "")
    {
        http_response_code($code);
        if (isset(self::$route[$code])) {
            self::$route[$code]->executeAction();
        } else {
            echo $message;
        }
        self::stopExecution();
    }

    private static function stopExecution()
    {
        die();
    }

    public static function getPath($name)
    {
        if (!isset(self::$route[$name])) {
            throw new Exception("Ceste route n'existe pas !");
        }
        return self::concatPath(self::getBaseUrl(), self::$route[$name]->path);
    }
}
