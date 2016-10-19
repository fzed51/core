<?php

use Kahlan\Arg;
use fzed51\Core\Route;


describe('Route test', function() {

    describe('Creation', function() {
        it('should create an route with action/controler action', function() {
            $new_route = new Route('name_path', 'uri', 'action@controleur');
            expect($new_route)->toBeA('object');
            expect($new_route)->toBeAnInstanceOf(\fzed51\Core\Route::class);
        });
        it('should create an route with callback action', function() {
            $new_route = new Route('name_path', 'uri', function(){});
            expect($new_route)->toBeA('object');
            expect($new_route)->toBeAnInstanceOf(\fzed51\Core\Route::class);
        });
    });

    describe('Property', function() {
        it('should get the path', function() {
            $new_route = new Route('name_path', 'uri', 'action@controleur');
            expect($new_route->getPath())->toBe('uri');
        });
    });

    describe('Path Analyse', function() {
        it('should interpret a simple path', function() {
            $new_route = new Route('name_path', 'uri', 'action@controleur');
            expect($new_route->pathToRegEx())->toBe('`^uri$`');
        });
        it('should interpret a simple path with simple parameter', function() {
            $new_route = new Route('name_path', 'uri/{param}', 'action@controleur');
            expect($new_route->pathToRegEx())->toBe('`^uri/(?<param>[A-Za-z0-9._-]+)$`');
        });
        it('should interpret a simple path with complex parameter', function() {
            $new_route = new Route('name_path', 'uri/{id}', 'action@controleur');
            $new_route->setRules([
                "id" => "[0-9]+"
            ]);
            expect($new_route->pathToRegEx())->toBe('`^uri/(?<id>[0-9]+)$`');
        });
        it('should interpret a complex path with complex parameter', function() {
            $new_route = new Route('name_path', 'uri/{id}/action/{hexa}', 'action@controleur');
            $new_route->setRules([
                "id" => "[0-9]+",
                "hexa" => "[0-9a-fA-F]{2}"
            ]);
            expect($new_route->pathToRegEx())->toBe('`^uri/(?<id>[0-9]+)/action/(?<hexa>[0-9a-fA-F]{2})$`');
        });
        it('should interpret a simple path with complex parameter with capturing group', function() {
            $new_route = new Route('name_path', 'uri/{slug}', 'action@controleur');
            $new_route->setRules([
                "slug" => "(A[b-z]+)|(B[ac-z]+)"
            ]);
            expect($new_route->pathToRegEx())->toBe('`^uri/(?<slug>(?:A[b-z]+)|(?:B[ac-z]+))$`');
        });
    });

    describe('Execution', function() {

        it('should execut an route with action/controler action', function() {
            class foo{ public function bar(){expect(true)->toBe(true);} public function bad(){expect(true)->toBe(false);} }
            $new_route = new Route('name_path', 'uri', 'bar@foo');
            $new_route->executeAction();
        });
        it('should execut an route with callback action', function() {
            $callback = function(){expect(true)->toBe(true);};
            $new_route = new Route('name_path', 'uri', $callback);
            $new_route->executeAction();
        });
        
    });

});

