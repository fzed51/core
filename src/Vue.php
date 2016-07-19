<?php

namespace fzed51\Core;

/**
 * Description of Section
 *
 * @author fabien.sanchez
 */
class Vue
{

    const WEBPATH = __DIR__ . DIRECTORY_SEPARATOR . '../public';

    private static $_base_root = null;
    private static $_style;
    private static $_style_cache = [];
    private static $_nav;
    private static $_content;
    private static $_content_cache = [];
    private static $_script;
    private static $_script_cache = [];

    public static function getBaseRoot()
    {
        if (is_null(self::$_base_root)) {
            self::$_base_root = dirname($_SERVER['SCRIPT_NAME']);
        }
        return self::$_base_root;
    }

    public static function addStyle($style, $first = false)
    {
        $key = md5($style);
        if (!isset(self::$_style_cache[$key])) {
            self::$_style_cache[$key] = true;
            if ($first) {
                self::$_style = '<style>' . $style . '</style>' . PHP_EOL . self::$_style;
            } else {
                self::$_style .= '<style>' . $style . '</style>' . PHP_EOL;
            }
        }
    }

    public static function addFileStyle($file_style, $first = false)
    {
        $key = md5($file_style);
        if (!isset(self::$_style_cache[$key])) {
            self::$_style_cache[$key] = true;
            $file_style = concatPath('./style', $file_style);
            if (file_exists(self::WEBPATH . DIRECTORY_SEPARATOR . $file_style)) {
                $root_file_style = concatPath(self::getBaseRoot(), $file_style);
                if ($first) {
                    self::$_style = '<link type="text/css" href="' . $root_file_style . '" rel="stylesheet" />' . PHP_EOL . self::$_style;
                } else {
                    self::$_style .= '<link type="text/css" href="' . $root_file_style . '" rel="stylesheet" />' . PHP_EOL;
                }
            }
        }
    }

    public static function style()
    {
        return self::$_style;
    }

    public static function setNav($nav)
    {
        self::$_nav = $nav;
    }

    public static function nav()
    {
        return self::$_nav;
    }

    public static function prependContent($content)
    {
        $key = md5($content);
        if (!isset(self::$_content_cache[$key])) {
            self::$_content_cache[$key] = true;
            self::$_content = $content . self::$_content;
        }
    }

    public static function setContent($content)
    {
        $key = md5($content);
        if (!isset(self::$_content_cache[$key])) {
            self::$_content_cache = [];
            self::$_content_cache[$key] = true;
            self::$_content = $content;
        }
    }

    public static function appendContent($content)
    {
        $key = md5($content);
        if (!isset(self::$_content_cache[$key])) {
            self::$_content_cache[$key] = true;
            self::$_content .= $content;
        }
    }

    public static function content()
    {
        return self::$_content;
    }

    public static function addScript($script, $first = false)
    {
        $key = md5($script);
        if (!isset(self::$_script_cache[$key])) {
            self::$_script_cache[$key] = true;
            if ($first) {
                self::$_script = '<script type="text/javascript" >' . $script . '</script>' . PHP_EOL . self::$_script;
            } else {
                self::$_script .= '<script type="text/javascript" >' . $script . '</script>' . PHP_EOL;
            }
        }
    }

    public static function addFileScript($file_script, $first = false)
    {
        $key = md5($file_script);
        if (!isset(self::$_script_cache[$key])) {
            self::$_script_cache[$key] = true;
            $file_script = concatPath('./script', $file_script);
            if (file_exists(self::WEBPATH . DIRECTORY_SEPARATOR . $file_script)) {
                $root_file_script = concatPath(self::getBaseRoot(), $file_script);
                if ($first) {
                    self::$_script = '<script type="text/javascript" src="' . $root_file_script . '"></script>' . PHP_EOL . self::$_script;
                } else {
                    self::$_script .= '<script type="text/javascript" src="' . $root_file_script . '"></script>' . PHP_EOL;
                }
            }
        }
    }

    public static function script()
    {
        return self::$_script;
    }

}
