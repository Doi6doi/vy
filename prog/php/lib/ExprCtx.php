<?php

namespace vy;

interface ExprCtx {

   const
      CONS = "cons",
      FUNC = "func",
      INFIX = "infix",
      ITEM = "item",
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
