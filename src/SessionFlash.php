<?php

namespace fzed51\Core;

class SessionFlash extends SessionModule {

    protected $name = 'Flash';

    protected function Register(){}

    private function getRawFlash()
    {

    }

    public function setFlash($type, $message)
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

    public function getFlashs()
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
