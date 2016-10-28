<?php

use fzed51\Core\Session;

describe('Session', function(){

    describe('Register', function(){

        it('should\'nt register the session if session is disabled', function(){
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_DISABLED);
            expect(function(){Session::register();})->toThrow(new Exception("Session : Impossible d'utiliser les sessions, elles sont desactivee."));
        });

        it('should\'nt register the session if header is send', function(){
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_NONE);
            allow('headers_sent')->toBeCalled()->andReturn(true);
            expect(function(){Session::register();})->toThrow(new Exception("Session : Impossible d'utiliser les sessions, une entete a deja ete envoyee. (0)"));
        });

        it('should throw an exception if start session is impossible', function(){
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_NONE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            allow('session_start')->toBeCalled()->andReturn(false);
            expect(function(){Session::register();})->toThrow(new Exception("Session : Impossible de demarrer une session"));
        });

        it('should create a session', function(){
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_NONE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            allow('session_start')->toBeCalled()->andReturn(true);
            expect(function(){Session::register();})->not->toThrow();
        });

        it('should create a session if it\'s started', function(){
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            expect(function(){Session::register();})->not->toThrow();
        });

    });

    describe('setter', function(){

        it('should write a value in session', function(){
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            allow(Session::class)->toReceive('write')->with('key', 'value')->andRun(function($key, $value){$_SESSION[$key] = $value;echo '$_SESSION[key]=value';});
            expect(function(){Session::set('key', 'value');})->toEcho('$_SESSION[key]=value');
        });

    });

});