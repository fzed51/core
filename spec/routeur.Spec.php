<?php

use Kahlan\Arg;
use fzed51\Core\Routeur;


describe('Routeur test', function() {

    describe('base URL', function(){

        it('should get the base URL', function(){
            $calculate = dirname($_SERVER['SCRIPT_NAME']);
            $calculate = str_replace("\\", "/", $calculate);
            $calculate = rtrim($calculate, '/');
            $calculate .= '/';
            expect(Routeur::getBaseUrl())->toBe($calculate);
        });

        it('should get a custom base URL', function(){
            Routeur::setBaseUrl('./ici/');
            expect(Routeur::getBaseUrl())->toBe('./ici/');
        });

        it('should get the base URL ended by /', function(){
            Routeur::setBaseUrl('./ici');
            expect(Routeur::getBaseUrl())->toBe('./ici/');
        });

    });

    describe('Route initialization', function(){

        it('should init a Route', function(){
            expect(Routeur::set('name_path', 'uri', 'action@controleur'))->toBeAnInstanceOf(\fzed51\Core\Route::class);
        });

    });
    
    describe('dispatch', function(){
        
        beforeEach(function(){
            Routeur::clear();
        });

        it('should dispatch Route from URI', function(){
            alow('die')->andRun(function(){});

            $this->runing1 = false;
            $this->runing2 = false;
            $this->callback1 = function() {$this->runing1 = true;};
            $this->callback2 = function() {$this->runing2 = true;};

            Routeur::set('name1', 'uri1', $this->callback1);
            Routeur::set('name2', 'uri2', $this->callback2);
            Routeur::dispatch('uri1');

            expect($this->runing1)->toBe(true);  
            expect($this->runing2)->not->toBe(true);            
        });
        it('should interpret the parameters in the URL and register them in $_GET', function(){
            alow('die')->andRun(function(){});

            $this->runing = false;
            $this->callback = function() {
                    $this->runing = true;
                    expect($_GET['id'])->toBeEq(123);
                };

            Routeur::set('name1', 'uri1/{id}', $this->callback);
            Routeur::dispatch('/uri1/123');
            
            expect($this->runing)->toBe(true);
        });

    });

    describe('redirection', function(){

        it('should redirect', function(){});

    });
    


});