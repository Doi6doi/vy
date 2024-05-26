<?php

namespace vy;

/// feltételes kifejezés
class StmIf
   extends Block
{

   const
      IF = "if";

   protected $cond;

   function __construct( Block $owner ) {
	  parent::__construct( $owner, $owner->kind() );
   }

   /// given olvasása
   function read( Stream $s ) {
      $s->readWS();
      $s->readToken( self::IF );
      $s->readWS();
      $s->readToken("(");
      $this->cond = $s->readExpr();
      $s->readToken(")");
      $s->readWS();
      if ( "{" == $s->next() ) {
         parent::read( $s );
      } else {
         $this->body [] = $s->readExpr();
         $s->readToken(";");
      }
   }


}

