<?php

namespace vy;

/// progrramnyelvi forrásfájl
abstract class Source
   implements ExprCtx
{

   abstract function read( ExprStream $s );

   function __construct() {
   }

   function blockKind() { return Block::NONE; }

   function defType() { return null; }

   function checkType($type) {
      if ( $type )
         throw new EVy("Unknown type: $type");
   }

   function readType( ExprStream $s ) {
      if ( Stream::IDENT == $s->nextKind() )
         return $s->read();
      else
         throw $s->expect("type");
   }

   function resolve( $token, $kind ) {
      return null;
   }

   function canCall( $x ) { return true; }


   /// beolvasás fájlból
   function readFile( $fname ) {
      $s = $this->createStream( $fname );
      try { try {
         $this->read( $s );
      } catch (Exception $e) {
         throw new EVy( $s->position().": ".$e->getMessage(),
            $e->getCode(), $e );
      }} finally {
         $s->close();
      }
   }

   function createStream( $fname ) {
      return new ExprStream( $fname );
   }


}
