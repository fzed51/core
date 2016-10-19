<?php

use Kahlan\Arg;
use fzed51\Core\Routeur;


describe('Routeur test', function() {

    describe('root path', function(){

        it('should get the root path', function(){});

    });

    describe('Route initialization', function(){

        it('should init a Route', function(){
            expect(Routeur::set('name_path', 'uri', 'action@controleur'))->toBeAnInstanceOf(\fzed51\Core\Route::class);
        });

    });
    
    describe('dispatch', function(){

        it('should dispatch Route from URI', function(){});

    });

    describe('redirection', function(){

        it('should redirect', function(){});

    });
    


});