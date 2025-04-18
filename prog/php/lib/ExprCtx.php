<?php

namespace vy;

interface ExprCtx {

   const
      FUNC = "func",
      INFIX = "infix",
      INTF = "intf",
      NAME = "name";

   /// alapértelmezett típus
   function defType();

   /// típusnév ellenőrzése
   function checkType( $type );

   /// típusnév olvasása
   function readType( Stream $s );

   /// token feloldása
   function resolve( $token, $kind );

   /// hívható-e az elem
   function canCall( $x );

}
