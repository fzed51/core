<?php

use fzed51\Core\Session;
use fzed51\Core\SessionModule;

describe('Session', function () {

    describe('Register', function () {
        it('should\'nt register the session if session is disabled', function () {
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_DISABLED);
            expect(function () {
                Session::register();
            })->toThrow(new Exception("fzed51\\Core\\Session : Impossible d'utiliser les sessions, elles sont desactivee."));
        });

        it('should\'nt register the session if header is send', function () {
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_NONE);
            allow('headers_sent')->toBeCalled()->andReturn(true);
            expect(function () {
                Session::register();
            })->toThrow(new Exception("fzed51\\Core\\Session : Impossible d'utiliser les sessions, une entete a deja ete envoyee. (0)"));
        });

        it('should throw an exception if start session is impossible', function () {
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_NONE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            allow('session_start')->toBeCalled()->andReturn(false);
            expect(function () {
                Session::register();
            })->toThrow(new Exception("fzed51\\Core\\Session : Impossible de demarrer une session"));
        });

        it('should create a session', function () {
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_NONE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            allow('session_start')->toBeCalled()->andReturn(true);
            expect(function () {
                Session::register();
            })->not->toThrow();
        });

        it('should create a session if it\'s started', function () {
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            expect(function () {
                Session::register();
            })->not->toThrow();
        });
    });

    describe('haser', function () {
        it('should determine whether a value exists', function () {
            $_SESSION = [];
            $_SESSION['key'] = 'value';

            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);

            expect(Session::has('key'))->toBeTruthy();
            expect(Session::has('key_unknow'))->toBeFalsy();
        });
    });

    describe('setter', function () {
        it('should write a value in session', function () {
            $_SESSION = [];

            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);

            Session::Set('key', 'value');
            expect($_SESSION['key'])->toBe('value');
        });
    });


    describe('getter', function () {
        it('should read a value in session', function () {            
            $_SESSION = [];
            $_SESSION['key']='value';

            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);

            expect(Session::get('key', 'x'))->toBe('value');
        });

        it('should read a default value if value do\'nt exist', function () {            
            $_SESSION = [];
            $_SESSION['key']='value';

            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            
            expect(Session::get('key_unknow'))->toBeNull();
            expect(Session::get('key_unknow', 'unknow'))->toBe('unknow');
        });
    });

    describe('module', function () {
        
        class module extends SessionModule {
            protected $name = "module";
            function register(){}
            function methode(){}
        }

        class notModule {
            protected $name = "notMudule";
            function register(){}
            function methode(){}
        }

        it('should accept the modules', function () {
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            expect(function(){Session::addModule(new module());})->not->toThrow();
        });

        it('should\'nt accept a class as a module', function () {
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            expect(function(){Session::addModule(new notModule());})->toThrow(new \Exception("fzed51\\Core\\Session : le module notModule n'est pas un module"));
        });

    });

});
