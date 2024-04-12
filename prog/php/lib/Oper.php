<?php

namespace vy;

/// operátor (prefix, infix, postfix )
class Oper {

   const
      INFIX = "infix",
      POSTFIX = "postfix",
      PREFIX = "prefix";

   protected $owner;
   protected $kind;
   protected $oper;

   function __construct( ExprCtx $owner ) {
      $this->owner = $owner;
   }

   function kind() { return $this->kind; }

   function oper() { return $this->oper; }

   function read( Stream $s ) {
      $s->readWS();
      switch ( $s->next() ) {
         case self::INFIX: case self::POSTFIX: case self::PREFIX:
            $this->kind = $s->read();
         break;
         default:
            throw $s->notexp( "operator" );
      }
      $this->readOper( $s );
Tools::debug("READ OPER", $this->oper);
      $s->readWS();
      $s->readToken(";");
   }

   /// operátor olvasása
   protected function readOper( $s ) {
      $s->readWS();
      $this->oper = "";
      while ( Tools::operCont( $this->oper, $s->next() ) )
         $this->oper .= $s->read();
      if ( ! $this->oper )
         throw $s->notexp("operator");
   }

}
