<?php

namespace vy;

/// feltételes kifejezés
class IfCond
   extends Conds
{

   const
      IF = "if";

   protected $cond;

   /// given olvasása
   function read( Stream $s ) {
      $s->readWS();
      $s->readToken( self::IF );
      $s->readWS();
      $s->readToken("(");
      $this->cond = $this->stack->readExpr( $s );
      $s->readToken(")");
      $s->readWS();
      if ( "{" == $s->next() ) {
         parent::read( $s );
      } else {
         $this->body [] = $this->stack->readExpr( $s );
         $s->readToken(";");
      }
   }


}

