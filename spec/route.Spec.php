<?php

describe('Test du routeur', function(){

    describe('Test de la partie static', function(){

        it('should create an route', function(){
            $new_route = fzed51\Core\Route::set('name_path', 'uri', 'action@controleur');
            expect($new_route)->toBeA('object');
            expect($new_route)->toBeAnInstanceOf(fzed51\Core\Route::class);
        });

    });

});

