<?php

namespace vy;

interface ExprCtx {

   const
      FUNC = "func",
      INFIX = "infix";

   function checkType( $type );

   function readType( Stream $s );

   function resolve( $token, $kind );

}
