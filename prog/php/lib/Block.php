<?php

namespace vy;

class Block 
   extends ExprCtxForward
   implements Stm
{

   const
      BODY = "body",
      COND = "cond";

   /// törzs fajta
   protected $kind;
   /// kifejezés verem
   protected $stack;
   /// utasítások
   protected $stms;

   function __construct( ExprCtx $owner, $kind ) {
	  parent::__construct( $owner );
	  $this->stms = [];
	  $this->kind = $kind;
   }

   function kind() { return $this->kind; }

   /// törzsrész olvasása
   function read( ExprStream $s ) {
      $s->readWS();
      $s->readToken("{");
      $s->push( $this, true );
      while ( $this->addStm( $s ) )
         ;
      $s->pop( true );
      $s->readToken("}");
   }

   // blokk futtatása
   function run( RunCtx $ctx ) {
	  $ret = null;
	  foreach ( $this->stms as $s ) {
		 $ret = $s->run( $ctx );
		 if ( $this->isTerm( $s ))
		    return $ret;
      }
      return $ret;
   }
   
   function isTerm( Stm $s ) {
	  return $s instanceof StmReturn;
   }
   
   /// egy elem olvasása
   protected function addStm( ExprStream $s ) {
      $s->readWS();
      if ( "}" == $s->next() )
         return false;
      $this->stms [] = $s->readStm();
      return true;
   }
	
}
