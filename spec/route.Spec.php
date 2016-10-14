<?php

use Kahlan\Plugin\Stub;


describe('Test du routeur', function() {

    it('should create an route with action/controler action', function() {
        $new_route = fzed51\Core\Route::set('name_path', 'uri', 'action@controleur');
        expect($new_route)->toBeA('object');
        expect($new_route)->toBeAnInstanceOf(\fzed51\Core\Route::class);
    });
    it('should create an route with callback action', function() {
        $new_route = fzed51\Core\Route::set('name_path', 'uri', function(){});
        expect($new_route)->toBeA('object');
        expect($new_route)->toBeAnInstanceOf(\fzed51\Core\Route::class);
    });

    beforeEach(function(){
        $this->actionUnknow = function(){

        };
        $new_route = fzed51\Core\Route::set('name_path', 'uri', 'action@controleur');
        $new_route = fzed51\Core\Route::set('404', '404', $this->actionUnknow);


    });

    describe('Test des Routes', function(){

    });

    describe('Test de la partie static', function() {
        
    });
});

