<?php

describe('Session', function(){

    describe('Register', function(){

        it('should register the session', function(){
            session_start();
            expect(true)->toBe(true);
        });

    });

});