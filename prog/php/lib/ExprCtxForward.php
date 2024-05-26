<?php

namespace vy;

class ExprCtxForward 
   implements ExprCtx
{

   protected $owner;

   function __construct( ExprCtx $owner ) {
	  $this->owner = $owner;
   }

   function owner() { return $this->owner; }

   function checkType( $type ) {
      $this->owner->checkType( $type );
   }

   function readType( Stream $s ) {
      return $this->owner->readType( $s );
   }
   
   function canCall( $x ) {
	  return $this->owner->canCall( $x );
   }

   function resolve( $token, $kind ) {
      return $this->owner->resolve( $token, $kind );
   }
   
}
