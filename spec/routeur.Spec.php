<?php

use Kahlan\Arg;
use fzed51\Core\Routeur;


describe(
    'Routeur test', function () {

        describe(
            'base URL', function () {

                it(
                    'should get the base URL', function () {
                        $calculate = dirname($_SERVER['SCRIPT_NAME']);
                        $calculate = str_replace("\\", "/", $calculate);
                        $calculate = rtrim($calculate, '/');
                        $calculate .= '/';
                        expect(Routeur::getBaseUrl())->toBe($calculate);
                    }
                );

                it(
                    'should get a custom base URL', function () {
                        Routeur::setBaseUrl('./ici/');
                        expect(Routeur::getBaseUrl())->toBe('./ici/');
                    }
                );

                it(
                    'should get the base URL ended by /', function () {
                        Routeur::setBaseUrl('./ici');
                        expect(Routeur::getBaseUrl())->toBe('./ici/');
                    }
                );

            }
        );

        describe(
            'Route initialization', function () {

                it(
                    'should init a Route', function () {
                        expect(Routeur::set('name_path', 'uri', 'action@controleur'))->toBeAnInstanceOf(\fzed51\Core\Route::class);
                    }
                );

            }
        );
    
        describe(
            'dispatch', function () {

                beforeEach(
                    function () {
                        Routeur::clear();
                    }
                );

                it(
                    'should dispatch Route from URI', function () {
                        allow(Routeur::class)->toReceive('::stopExecution')->andRun(
                            function () {
                            }
                        );

                        $callback1 = function () {
                            echo "callback n°1 OK";
                        };
                        $callback2 = function () {
                            echo "callback n°2 OK";
                        };

                        Routeur::set('name1', 'uri1', $callback1);
                        Routeur::set('name2', 'uri2', $callback2);
            
                        $closure1 = function () {
                            Routeur::dispatch('uri1');
                        };
                        $closure2 = function () {
                            Routeur::dispatch('uri2');
                        };
                        expect($closure1)->toEcho('callback n°1 OK');
                        expect($closure2)->toEcho('callback n°2 OK');
                    }
                );

                it(
                    'should interpret the parameters in the URI and register them in $_GET', function () {
                        allow(Routeur::class)->toReceive('::stopExecution')->andRun(
                            function () {
                            }
                        );

                        $callback = function () {
                            if(isset($_GET['id'])) {
                                echo 'id : ' . $_GET['id'];
                            } else {
                                echo 'id is na';
                            }
                        };

                        Routeur::set('name1', 'uri1/{id}', $callback);
            
                        $closure = function () {
                            Routeur::dispatch('/uri1/123');
                        };
            
                        expect($callback)->toEcho('id is na');
                        expect($closure)->toEcho('id : 123');
                    }
                );

            }
        );

        describe(
            'redirection', function () {

                beforeEach(
                    function () {
                        Routeur::clear();
                    }
                );

                it(
                    'should redirect to 404 status page', function () {
                        allow(Routeur::class)->toReceive('::stopExecution')->andRun(
                            function () {
                            }
                        );

                        $closure = function () {
                            Routeur::dispatch('unknow');
                        };
                        expect($closure)->toEcho('Page introuvable ...');
                        expect(http_response_code())->toEqual(404);
                    }
                );

                it(
                    'should redirect to specific page with 404 status', function () {
                        allow(Routeur::class)->toReceive('::stopExecution')->andRun(
                            function () {
                            }
                        );

                        $callback = function () {
                            echo "Error 404";
                        };
                        Routeur::set('404', '404', $callback);

                        $closure = function () {
                            Routeur::dispatch('unknow');
                        };
                        expect($closure)->toEcho('Error 404');
                        expect(http_response_code())->toEqual(404);
                    }
                );

                it(
                    'should redirect to 500 status page for exception', function () {
                        allow(Routeur::class)->toReceive('::stopExecution')->andRun(
                            function () {
                            }
                        );

                        Routeur::set(
                            'bad', 'bad', function () {
                                throw new \Exception();
                            }
                        );

                        $closure = function () {
                            Routeur::dispatch('bad');
                        };
                        expect($closure)->toEcho('Erreur serveur ...');
                        expect(http_response_code())->toEqual(500);
                    }
                );

                it(
                    'should redirect to specific page with 500 status for exception', function () {
                        allow(Routeur::class)->toReceive('::stopExecution')->andRun(
                            function () {
                            }
                        );

                        Routeur::set(
                            'bad', 'bad', function () {
                                throw new \Exception();
                            }
                        );
                        Routeur::set(
                            '500', '500', function () {
                                echo "erreur serveur";
                            }
                        );

                        $closure = function () {
                            Routeur::dispatch('bad');
                        };
                        expect($closure)->toEcho('erreur serveur');
                        expect(http_response_code())->toEqual(500);
                    }
                );

            }
        );

        describe(
            'url', function () {

                beforeEach(
                    function () {
                        Routeur::clear();
                        Routeur::setBaseUrl('./ici/');
                    }
                );

                it(
                    'should get the URL path from Route', function () {
                        Routeur::set(
                            'name', 'uri', function () {
                            }
                        );
                        expect(Routeur::urlFor('name'))->toEqual('./ici/uri');
                    }
                );
                it(
                    'should get the URL path from Route with parameters', function () {
                        Routeur::set(
                            'name', 'uri/{id}/edit', function () {
                            }
                        );
                        expect(Routeur::urlFor('name', ['id'=>12]))->toEqual('./ici/uri/12/edit');
                    }
                );
                it(
                    'should get the URL path from Route with query', function () {
                        Routeur::set(
                            'name', 'uri', function () {
                            }
                        );
                        expect(Routeur::urlFor('name', [], ['id'=>12]))->toEqual('./ici/uri?id=12');
                    }
                );

            }
        );

        describe(
            'collection', function () {

                beforeEach(
                    function () {
                        Routeur::clear();
                        Routeur::setBaseUrl('./ici/');
                    }
                );

                it(
                    'should get the list of route path', function () {
                        Routeur::set(
                            'name', 'uri', function () {
                            }
                        );
                        Routeur::set(
                            'name2', 'uri/{id}/edit', function () {
                            }
                        );
            
                        expect(Routeur::getPaths())->toBe(
                            [
                            'name' => './ici/uri',
                            'name2' => './ici/uri/{id}/edit',
                            ]
                        );
                    }
                );
                it(
                    'should get the list of route path in json', function () {
                        Routeur::set(
                            'name', 'uri', function () {
                            }
                        );
                        Routeur::set(
                            'name2', 'uri/{id}/edit', function () {
                            }
                        );
            
                        expect(Routeur::getPaths('json'))->toBe("{\"name\":\".\\/ici\\/uri\",\"name2\":\".\\/ici\\/uri\\/{id}\\/edit\"}");
                    }
                );
            }
        );
    


    }
);
