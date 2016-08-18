<?php

ini_set('html_errors', 'on');
ini_set('error_prepend_string', '<div style="color: #000;text-shadow: #bbb 1px 1px 1px;white-space:pre-wrap;background: repeating-linear-gradient(45deg,rgba(255,235,59,0.2),rgba(255,235,59,0.25) 10px,rgba(244,67,54,0.2) 10px,rgba(244,67,54,0.25) 20px) rgba(255,255,255,0.5);border:#f00 solid 0.5em;border-radius: 10px;margin: 10px;padding: 0 1em;font: 17px monospace;line-height: 15px;">');
ini_set('error_append_string', '<br/></div>');

set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {

    $backtrace = debug_backtrace();
    //array_shift($backtrace); // elimine la fonction de handler

    echo '<div class="error">';
    switch ($errno) {
        case E_USER_ERROR:
            echo "<b>ERREUR</b> [$errno] $errstr<br />\n";
            echo "  Erreur fatale sur la ligne $errline dans le fichier $errfile";
            echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            echo "Arrêt...<br />\n";
            exit(1);
            break;

        case E_WARNING :
        case E_USER_WARNING:
            echo "<b>ALERTE</b> [$errno] $errstr<br />\n";
            echo "sur la ligne <strong>$errline</strong> dans le fichier <strong>$errfile</strong><br />\n";
            echo '<pre>' . print_r($backtrace, true) . '</pre>';
            break;

        case E_NOTICE:
        case E_USER_NOTICE:
            echo "<b>AVERTISSEMENT</b> [$errno] $errstr<br />\n";
            echo "sur la ligne <strong>$errline</strong> dans le fichier <strong>$errfile</strong><br />\n";
            echo '<pre>' . print_r($backtrace, true) . '</pre>';
            break;

        default:
            echo "Type d'erreur inconnu : [$errno] $errstr<br />\n";
            echo "sur la ligne <strong>$errline</strong> dans le fichier <strong>$errfile</strong><br />\n";
            echo '<pre>' . print_r($backtrace, true) . '</pre>';
            break;
    }
    echo '</div>';
    return true;
});
