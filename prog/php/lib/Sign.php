<?php

namespace vy;

/// függvény paraméterek és visszatérési érték
class Sign
   implements ExprCtx
{

   /// tulajdonos
   protected $owner;
   /// argumentumok
   protected $args;
   /// visszatérési érték
   protected $result;

   function __construct( ExprCtx $owner ) {
      $this->owner = $owner;
      $this->args = [];
   }

   /// olvasás
   function read( Stream $s ) {
      $s->readWS();
      if ( $s->readIf("(") ) {
         while ( $this->readArg($s) )
            ;
         $s->readToken(")");
         $s->readWS();
      }
      $this->readResult( $s );
   }

   /// visszatérési típus olvasása
   function readResult( Stream $s ) {
      $s->readWS();
      if ( $s->readIf(":") )
         $this->result = $this->readType( $s );
   }

   function checkType( $type ) {
      $this->owner->checkType( $type );
   }

   function readType( Stream $s ) {
      return $this->owner->readType( $s );
   }

   function resolve( $token, $kind ) { return null; }

   function __toString() { return $this->dump(); }

   function dump() {
      return sprintf( "(%s):%s", implode(",",$this->args), $this->result );
   }


   /// argumentum olvasás
   protected function readArg( $s ) {
      $s->readWS();
      if ( ")" == $s->next() )
         return false;
      if ( $this->args )
         $s->readToken(",");
      $ret = new Arg( $this );
      $ret->read( $s );
      $this->args [] = $ret;
      return true;
   }

}
