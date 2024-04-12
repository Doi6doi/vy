<?php

namespace vy;

interface ExprCtx {

   const
      FUNC = "func",
      NAME = "name",
      INFIX = "infix";

   function checkType( $type );

   function readType( Stream $s );

   function resolve( $token, $kind );

}
