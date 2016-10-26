<?php

if (isset($_SESSION['CSRF'])) {
    $_SESSION['OLD_CSRF'] = $_SESSION['CSRF'];
}
$_SESSION['CSRF'] = sha1(uniqid() . time() . 'FabienSanchez');

function inputCsrf()
{
    return '<input type="hidden" name="CSRF" value="' . $_SESSION['CSRF'] . '">';
}

function checkPostCsrf()
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

function checkGetCsrf()
{
    if (!isset($_SESSION['OLD_CSRF']) || !isset($_GET['CSRF']) || $_SESSION['OLD_CSRF'] <> $_GET['CSRF']) {
        setFlash('error', "Vous n'etes pas autorisé  à effectuer cette action.");
        redirect(401, url('home'));
        exit(1);
    }
}

function csrfBack()
{
    $_SESSION['CSRF'] = $_SESSION['OLD_CSRF'];
}

function getCsrf()
{
    return $_SESSION['CSRF'];
}

function setFlash($type, $message)
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

define("FLASH_ERROR", "error");
define("FLASH_WARNING", "warning");
define("FLASH_SUCCES", "succes");
define("FLASH_INFO", "info");

function getFlashs()
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
