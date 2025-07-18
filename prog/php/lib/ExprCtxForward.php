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

   function defType() {
      return $this->owner->defType();
   }

   function readType( ExprStream $s ) {
      return $this->owner->readType( $s );
   }
   
   function canCall( $x ) {
	  return $this->owner->canCall( $x );
   }

   function blockKind() {
      return $this->owner->blockKind();
   }

   function resolve( $token, $kind ) {
      return $this->owner->resolve( $token, $kind );
   }
   
}
