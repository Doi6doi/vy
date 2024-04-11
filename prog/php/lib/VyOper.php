7<?php

/// operátor (prefix, infix, postfix )
class VyOper {

   const
      INFIX = "infix",
      POSTFIX = "postfix",
      PREFIX = "prefix";

   protected $owner;
   protected $kind;
   protected $oper;
   protected $pref;

   function __construct( VyExprReader $owner ) {
      $this->owner = $owner;
   }

   function read( VyStream $s ) {
      $s->readWS();
      switch ( $s->next() ) {
         case self::INFIX: case self::POSTFIX: case self::PREFIX:
            $this->kind = $s->read();
         break;
         default:
            throw $s->notexp( "operator" );
      }
      $s->readWS();
      while ( VyStream::SYMBOL == $s->nextKind() )
         $this->oper .= $s->read();
      $s->readWS();
      $this->pref = $s->readNat();
      $s->readWS();
      $s->readToken(";");
   }

}
