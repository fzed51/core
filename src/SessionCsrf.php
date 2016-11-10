<?php

namespace fzed51\Core;

class SessionCsrf extends SessionModule {

    protected $name = 'CSRF';

    function Register()
    {
        if ($this->session->has('CSRF')) {
            $this->session->write('OLD_CSRF', $this->session->read('CSRF'));
        }
        $this->session->write('CSRF', sha1(uniqid() . time() . 'FabienSanchez'));
    }

    static function inputCsrf()
    {
        return '<input type="hidden" name="CSRF" value="' . $this->session->read('CSRF') . '">';
    }

    function checkPostCsrf()
    {
        if (!$this->session->has('OLD_CSRF') || !$this->session->has('CSRF') || $this->session->read('OLD_CSRF') <> $_POST['CSRF']) {
            if (isAjaxMethode()) {
                http_response_code(401);
            } else {
                Session::setFlash('error', "Vous n'etes pas autorisé  à effectuer cette action.");
                redirect(401, url('home'));
            }
            $this->stopExecution();
        }
    }

    function checkGetCsrf()
    {
        if (!$this->session->has('OLD_CSRF') || !$this->session->has('CSRF') || $this->session->read('OLD_CSRF') <> $_GET('CSRF')) {
            Session::setFlash('error', "Vous n'etes pas autorisé  à effectuer cette action.");
            redirect(401, url('home'));
            $this->stopExecution();
        }
    }

    private function stopExecution()
    {
        die();
    }

    function csrfBack()
    {
        $this->session->write('CSRF', $this->session->read('OLD_CSRF'));
    }

    function getCsrf()
    {
        return $this->session->read('CSRF');
    }

}
