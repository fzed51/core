<?php

use fzed51\Core\Session;
use fzed51\Core\SessionModule;
use fzed51\Core\SessionFlash;

describe('Flash Module Session', function () {

        describe('register', function(){

            it('should be registred as a Session module', function(){
                allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
                allow('headers_sent')->toBeCalled()->andReturn(false);
                expect(function () {
                    Session::addModule(new SessionFlash());
                })->not->toThrow();
            });

        });

});