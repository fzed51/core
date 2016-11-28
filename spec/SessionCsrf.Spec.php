<?php

use fzed51\Core\{
    Session, SessionCsrf, SessionModule
};

describe('Csrf Module Session', function () {
    describe('register', function () {
        it('should be registred as a Session module', function () {
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            expect(function () {
                Session::addModule(new SessionCsrf());
            })->not->toThrow();
        });
        it('should be registred with all methodes', function () {
            $listMethodes = Session::listeMethodes();
            expect($listMethodes)->toContainKey(
                    'inputCsrf', 'checkPostCsrf', 'checkGetCsrf', 'csrfBack', 'getCsrf'
            );
        });
        it('should initializ the data', function () {
            $_SESSION = [];
            Session::raz();
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            expect(function () {
                Session::addModule(new SessionCsrf());
            })->not->toThrow();
            expect($_SESSION)->toContainKey('CSRF');
            expect($_SESSION)->not->toContainKey('OLD_CSRF');
        });
        it('should reinitializ the data', function () {
            Session::raz();
            $_SESSION = ['CSRF' => '0123456789abcdef0123456789abcdef01234567'];
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            expect(function () {
                Session::addModule(new SessionCsrf());
            })->not->toThrow();
            expect($_SESSION)->toContainKey('CSRF');
            expect($_SESSION)->toContainKey('OLD_CSRF');
        });
    });

    describe('getter', function(){
        it('should return the raw CSRF token', function(){
            Session::raz();
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            expect(function () {
                Session::addModule(new SessionCsrf());
            })->not->toThrow();
            $_SESSION = ['CSRF' => '0123456789abcdef0123456789abcdef01234567'];
            expect(Session::getCsrf())->toEqual('0123456789abcdef0123456789abcdef01234567');
            
        });
    });

    describe('undo', function(){
        it('should unregistred the new CSRF token', function(){
            Session::raz();
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            $_SESSION = ['CSRF' => '0123456789abcdef0123456789abcdef01234567'];
            expect(function () {
                Session::addModule(new SessionCsrf());
            })->not->toThrow();
            Session::csrfBack();
            expect(Session::getCsrf())->toEqual('0123456789abcdef0123456789abcdef01234567');
        });
    });

    describe('input', function(){
        it('should get a input hidden field for the token', function(){
            Session::raz();
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            expect(function () {
                Session::addModule(new SessionCsrf());
            })->not->toThrow();
            $_SESSION = ['CSRF' => '0123456789abcdef0123456789abcdef01234567'];
            expect(Session::inputCsrf())->toEqual("<input type=\"hidden\" name=\"CSRF\" value=\"0123456789abcdef0123456789abcdef01234567\">");
        });
    });

    describe('check', function(){
        it('should check if a tocken give in POST is valid ', function(){
            Session::raz();
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            expect(function () {
                Session::addModule(new SessionCsrf());
            })->not->toThrow();
            $_SESSION = ['CSRF' => '0123456789abcdef0123456789abcdef01234567'];
            $_POST['CSRF'] = '0123456789abcdef0123456789abcdef01234567';
            expect(function(){Session::checkPostCsrf();})->not->toThrow();
            expect(http_response_code())->not->toBe(401);
        });
        it('should check if a tocken give in POST is not valid ', function(){
            Session::raz();
            allow('session_status')->toBeCalled()->andReturn(PHP_SESSION_ACTIVE);
            allow('headers_sent')->toBeCalled()->andReturn(false);
            expect(function () {
                Session::addModule(new SessionCsrf());
            })->not->toThrow();
            $_SESSION = ['CSRF' => '0123456789abcdef0123456789abcdef01234567'];
            $_POST['CSRF'] = '0123456789abcdef0123456789abcdef01234567';
            $_POST['CSRF'] = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
            expect(function(){Session::checkPostCsrf();})->not->toThrow();
            expect(http_response_code())->toBe(401);
        });
    });
});
