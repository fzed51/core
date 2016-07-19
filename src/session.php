<?php

/*
 * The MIT License
 *
 * Copyright 2015 fabien.sanchez.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

if (isset($_SESSION['CSRF'])) {
    $_SESSION['OLD_CSRF'] = $_SESSION['CSRF'];
}
$_SESSION['CSRF'] = sha1(uniqid() . time() . 'FabienSanchez');

function inputCsrf() {
    return '<input type="hidden" name="CSRF" value="' . $_SESSION['CSRF'] . '">';
}

function checkPostCsrf() {
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

function checkGetCsrf() {
    if (!isset($_SESSION['OLD_CSRF']) || !isset($_GET['CSRF']) || $_SESSION['OLD_CSRF'] <> $_GET['CSRF']) {
        setFlash('error', "Vous n'etes pas autorisé  à effectuer cette action.");
        redirect(401, url('home'));
        exit(1);
    }
}

function csrfBack() {
    $_SESSION['CSRF'] = $_SESSION['OLD_CSRF'];
}

function getCsrf() {
    return $_SESSION['CSRF'];
}

function setFlash($type, $message) {
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

function getFlashs() {
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
