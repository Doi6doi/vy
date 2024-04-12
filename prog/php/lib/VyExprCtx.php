<?php

interface VyExprCtx {

   const
      FUNC = "func",
      INFIX = "infix";

   function checkType( $type );

   function readType( VyStream $s );

   function resolve( $token, $kind );

}
