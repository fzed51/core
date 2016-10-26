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

    public static function urlFor($name, array $options = [], array $query = [])
    {
        $base = self::getBaseUrl();
        if (!isset(self::$route[$name])) {
            return self::concatPath($base, '/', '/');
        }
        return self::concatPath($base, self::$route[$name]->getUrl($options, $query), '/');
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

    public static function getPaths($format = 'php')
    {
        $liste = [];
        foreach (self::$route as $name => $route) {
            $liste[$name] = self::concatPath(self::getBaseUrl(), self::$route[$name]->getPath());
        }
        switch($format){
            case 'php':
                return $liste;
            case 'json':
                return json_encode($liste);
            default:
                throw new \Exception("format non supporté");
        }
    }
}
