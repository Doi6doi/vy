<?php

namespace vy;

interface ExprCtx {

   /// resolve lehetőségek
   const
      CONS = "cons",
      FUNC = "func",
      INFIX = "infix",
      ITEM = "item",
      NAME = "name";

   function blockKind();

   /// alapértelmezett típus
   function defType();

   /// típusnév ellenőrzése
   function checkType( $type );

   /// típusnév olvasása
   function readType( ExprStream $s );

   /// token feloldása
   function resolve( $token, $kind );

   /// hívható-e az elem
   function canCall( $x );

}
