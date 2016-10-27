<?php

describe('Session', function(){

    describe('Register', function(){

        it('should register the session', function(){
            expect(session_status())->toBe(PHP_SESSION_DISABLED );
            expect(session_status())->toBe(PHP_SESSION_NONE );
            expect(session_status())->toBe(PHP_SESSION_ACTIVE );
        });

    });

});