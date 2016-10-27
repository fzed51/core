<?php

namespace fzed51\Core;

define("FLASH_ERROR", "error");
define("FLASH_WARNING", "warning");
define("FLASH_SUCCES", "succes");
define("FLASH_INFO", "info");

class Session
{

    static private $registred = false;

    static function register()
    {
        if (!self::isStart()) {
            self::start();
        }
        if (self::isStart()) {
            self::initializ();
            return true;
        } else {
            return false;
        }
    }

    private static function isEnviable()
    {
        $status = session_status();
        if ($status == PHP_SESSION_DISABLED) {
            throw new \Exception("Impossible d'utiliser les sessions, elles sont desactivee.");
        }
        $file = '';
        $line = 0;
        if ($status == PHP_SESSION_NONE && headers_sent($file, $line)) {
            throw new \Exception("Impossible d'utiliser les sessions, une entete a deja ete envoyee. {$file}({$line})");
        }
    }

    private static function isStart()
    {
        $status = session_status();
        if (self::isEnviable()) {
            if ($status == PHP_SESSION_ACTIVE) {
                return true;
            } else {
                return false;
            }
        }
    }

    static function start()
    {
        if (!session_start()) {
            throw new \Exception("Impossible de demarrer une session.");
        }
        return true;
    }

    static function stop()
    {
        session_write_close();
    }

    static function destroy()
    {
        if (isset($_SESSION)) {
            $_SESSION = [];
        }

        session_unset();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        if (!session_destroy()) {
            throw new \Exception("Impossible de detruire la session.");
        }
    }

    static function initializ()
    {

        if (isset($_SESSION['CSRF'])) {
            $_SESSION['OLD_CSRF'] = $_SESSION['CSRF'];
        }
        $_SESSION['CSRF'] = sha1(uniqid() . time() . 'FabienSanchez');
    }

    static function inputCsrf()
    {
        return '<input type="hidden" name="CSRF" value="' . $_SESSION['CSRF'] . '">';
    }

    static function checkPostCsrf()
    {
        if (!isset($_SESSION['OLD_CSRF']) || !isset($_POST['CSRF']) || $_SESSION['OLD_CSRF'] <> $_POST['CSRF']) {
            if (isAjaxMethode()) {
                http_response_code(401);
            } else {
                setFlash('error', "Vous n'etes pas autorisé  à effectuer cette action.");
                redirect(401, url('home'));
            }
            exit(1);
        }
    }

    static function checkGetCsrf()
    {
        if (!isset($_SESSION['OLD_CSRF']) || !isset($_GET['CSRF']) || $_SESSION['OLD_CSRF'] <> $_GET['CSRF']) {
            setFlash('error', "Vous n'etes pas autorisé  à effectuer cette action.");
            redirect(401, url('home'));
            exit(1);
        }
    }

    static function csrfBack()
    {
        $_SESSION['CSRF'] = $_SESSION['OLD_CSRF'];
    }

    static function getCsrf()
    {
        return $_SESSION['CSRF'];
    }

    static function setFlash($type, $message)
    {
        $type = strtolower($type);
        if (!isset($_SESSION['FLASH'])) {
            $_SESSION['FLASH'] = [];
        }
        if (!isset($_SESSION['FLASH'][$type])) {
            $_SESSION['FLASH'][$type] = [];
        }
        $_SESSION['FLASH'][$type][] = $message;
    }

    static function getFlashs()
    {
        $out = '';
        if (isset($_SESSION['FLASH'])) {
            foreach ($_SESSION['FLASH'] as $type => $flashs) {
                foreach ($flashs as $message) {
                    $out .= "<div class=\"flash {$type}\">$message</div>" . PHP_EOL;
                }
            }
        }
        $_SESSION['FLASH'] = [];
        return $out;
    }
}
