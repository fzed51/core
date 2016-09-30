<?php

namespace fzed51\Core;

/**
 * Description of Config
 *
 * @author fabien.sanchez
 */
class Config {

    static private $Init = false;
    static private $Config = [];

    static public function initializ($configFileName = '') {
        self::registerDotEnv(self::parseDotEnv());
        if ($configFileName != '') {
            self::registerConfig(self::parseConfigFile($configFileName));
        }
        self::$Init = true;
    }

    static public function get($key, $defaut = null) {
        self::autoInitializ();
        if (self::has($key)) {
            return self::$Config[$key];
        }
        return $defaut;
    }

    static public function set($key, $valuye) {
        self::autoInitializ();
        self::$Config[$key] = $valuye;
    }

    static public function has($key) {
        if (isset(self::$Config[$key])) {
            return true;
        }
        return false;
    }

    static private function autoInitializ() {
        if (!self::$Init) {
            self::initializ();
        }
    }

    static private function parseDotEnv() {
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

    static private function parseConfigFile($configFileName) {
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

    static private function registerDotEnv(array $dotEnv) {
        foreach ($dotEnv as $key => $value) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }

    static private function registerConfig(array $config) {
        self::$Config = $config;
    }

}
