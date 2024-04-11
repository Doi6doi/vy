<?php

interface VyExprCtx {

   function checkType( $type );

   function readType( VyStream $s );

   function resolve( $token );

}
