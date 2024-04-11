<?php

/// függvény paraméterek és visszatérési érték
class VySign implements VyExprReader {

   /// tulajdonos
   protected $owner;
   /// argumentumok
   protected $args;
   /// visszatérési érték
   protected $result;

   function __construct( VyExprReader $owner ) {
      $this->owner = $owner;
      $this->args = [];
   }

   /// olvasás
   function read( VyStream $s ) {
      $s->readWS();
      if ( $s->readIf("(") ) {
         while ( $this->readArg($s) )
            ;
         $s->readToken(")");
         $s->readWS();
      }
      if ( $s->readIf(":") ) {
         $this->result = $this->readType( $s );
      }
   }

   function checkType( $type ) {
      $this->owner->checkType( $type );
   }

   function readType( VyStream $s ) {
      return $this->owner->readType( $s );
   }

   /// argumentum olvasás
   protected function readArg( $s ) {
      $s->readWS();
      if ( ")" == $s->next() )
         return false;
      if ( $this->args )
         $s->readToken(",");
      $ret = new VyArg( $this );
      $ret->read( $s );
      $this->args [] = $ret;
      return true;
   }

}
