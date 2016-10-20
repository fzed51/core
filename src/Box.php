<?php

namespace fzed51\Core;

/**
 * Description de la classe Box
 *
 * Conteneur d'injection de dépendance
 * @author fabien.sanchez
 * @copyright 2015
 *
 */
class BoxException extends \Exception
{

}

class Box
{

    static private $generateur = [];
    static private $instance = [];

    static function set($key, callable $callback, $singleton)
    {
        self::$generateur[$key] = [
            'callback' => $callback,
            'singleton' => $singleton
        ];
    }

    static function get($key)
    {
        if (isset(self::$generateur[$key])) {
            $gen = self::$generateur[$key];
            if ($gen['singleton']) {
                return self::getInstance($key);
            } else {
                return self::getCallback($key);
            }
        } else {
            throw new BoxException("La boite '$key' n'existe pas !");
        }
    }

    private static function getInstance($key)
    {
        if (!isset(self::$instance[$key])) {
            self::$instance[$key] = self::getCallback($key);
        }
        return self::$instance[$key];
    }

    private static function getCallback($key)
    {
        return call_user_func(self::$generateur[$key]['callback']);
    }
}
