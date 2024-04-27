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

   function args() { return $this->args; }

   function result() { return $this->result; }

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
      if ( ! $s->readIf(":") ) 
         return false;
      $this->result = $this->readType( $s );
      return true;   
   }

   /// kompatibilitás ellenőrzése
   function checkCompatible( Sign $other, array $map ) {
      if ( $n = count( $this->args() ) != count( $other->args() ) )
         throw $this->notComp( $other, "arg count");
      if ( $this->result() != Tools::gc( $map, $other->result()))
         throw $this->notComp( $other, "result");
      for( $i=0; $i<$n; ++$i)
         $this->args()[$i]->checkCompatible( $other->args()[$i], $map );
   }

   /// paraméterlista megfeleltetése
   function inherit( Sign $other, array $map ) {
      $this->args = [];
      foreach ( $other->args() as $oa ) {
         $a = new Arg($this, $oa->name(), Tools::gc( $map, $oa->type() ));
         $this->args [] = $a;
      }
      if ( $r = $other->result() )
         $this->result = Tools::gc( $map, $r );
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

   protected function notComp( Sign $other, $reason ) {
      return new EVy("Not compatible sign: ".
         $this->owner()->name().": ".$result );
   }

}
