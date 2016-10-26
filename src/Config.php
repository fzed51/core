<?php

namespace fzed51\Core;

/**
 * Description of Config
 *
 * @author fabien.sanchez
 */
class Config
{

    static private $Init = false;
    static private $Config = [];

    public static function initializ($configFileName = '')
    {
        self::registerDotEnv(self::parseDotEnv());
        if ($configFileName != '') {
            self::registerConfig(self::parseConfigFile($configFileName));
        }
        self::$Init = true;
    }

    public static function get($key, $defaut = null)
    {
        self::autoInitializ();
        if (self::has($key)) {
            return self::$Config[$key];
        }
        return $defaut;
    }

    public static function set($key, $valuye)
    {
        self::autoInitializ();
        self::$Config[$key] = $valuye;
    }

    public static function has($key)
    {
        if (isset(self::$Config[$key])) {
            return true;
        }
        return false;
    }

    private static function autoInitializ()
    {
        if (!self::$Init) {
            self::initializ();
        }
    }

    private static function parseDotEnv()
    {
        $env = [];
        if (file_exists('.env')) {
            $envFile = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($envFile as $line) {
                $line = ltrim($line);
                if ($line[1] == ';' || $line[1] == '#') {
                    continue;
                }
                list($key, $value) = explode("=", $line, 2);
                $env[strtoupper(trim($key))] = trim($value);
            }
        }
        return $env;
    }

    private static function parseConfigFile($configFileName)
    {
        try {
            if (file_exists($configFileName)) {
                $config = include($configFileName);
                if (is_array($config)) {
                    return $config;
                }
            }
        } catch (Exception $ex) {
            // Pas d'action
        }
        return [];
    }

    private static function registerDotEnv(array $dotEnv)
    {
        foreach ($dotEnv as $key => $value) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }

    private static function registerConfig(array $config)
    {
        self::$Config = $config;
    }
}
