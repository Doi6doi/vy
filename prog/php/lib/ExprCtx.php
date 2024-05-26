<?php

namespace vy;

interface ExprCtx {

   const
      FUNC = "func",
      INFIX = "infix",
      INTF = "intf",
      NAME = "name";

   function checkType( $type );

   function readType( Stream $s );

   /// token feloldása
   function resolve( $token, $kind );

   /// hívható-e az elem
   function canCall( $x );

}
