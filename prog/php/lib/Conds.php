<?php

namespace vy;

/// feltételek az elő-utófeltétleknél
class Conds
   implements ExprCtx
{

   /// tulajdonos
   protected $owner;
   /// törzsrész
   protected $body;
   /// kifejezés verem
   protected $stack;

   function __construct( ExprCtx $owner ) {
      $this->owner = $owner;
      $this->body = [];
      $this->stack = new Stack($this);
   }

   function checkType( $t ) { return $this->owner->checkType($t); }

   function readType( $t ) { return $this->owner->readType($t); }

   function resolve( $s, $kind ) { return $this->owner->resolve($s,$kind); }

   /// törzsrész olvasása
   function read( Stream $s ) {
      $s->readWS();
      $s->readToken("{");
      $this->stack = new Stack($this);
      while ( $this->readItem( $s ))
         ;
      $this->stack = null;
      $s->readToken("}");
   }

   /// egy elem olvasása
   function readItem( Stream $s ) {
      $s->readWS();
      if ( "}" == $s->next() )
         return false;
      if ( Given::GIVEN == $s->next() ) {
         $ret = new Given( $this );
         $ret->read( $s );
      } else {
         $ret = $this->stack->readExpr( $s );
         $s->read(";");
      }
      $this->body [] = $ret;
      return true;
   }


}
