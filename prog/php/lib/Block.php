<?php

namespace vy;

class Block 
   extends ExprCtxForward
   implements Stm
{

   const
      BODY = "body",
      COND = "cond",
      NONE = "none";

   /// kifejezés verem
   protected $stack;
   /// utasítások
   protected $stms;
   /// blokk helye
   protected $position;

   function __construct( ExprCtx $owner ) {
	  parent::__construct( $owner );
	  $this->stms = [];
   }

   function blockKind() { return $this->owner->blockKind(); }

   /// törzsrész olvasása
   function read( ExprStream $s ) {
      $s->readWS();
      $this->position = $s->position();
      $this->readPart( $s, true );
   }

   /// esetleg zárójelzett rész olvasása
   function readPart( ExprStream $s, $mustBlock = false ) {
      $s->readWS();
	   $s->push( $this, true );
	   if ( $mustBlock || "{" == $s->next() ) {
		    $s->readToken( "{" );
		    while ( $ret = $this->addStm( $s ) )
		       ;
		    $s->readToken( "}" );
	   } else
	      $ret = $this->addStm( $s );
      $s->pop( true );
      return $ret;
   }

   // blokk futtatása
   function run( RunCtx $ctx ) {
	  try {
        $ret = null;
	     foreach( $this->stms as $s ) {
		    $ret = $s->run( $ctx );
		    if ( Cont::term( $ret, Cont::BLOCK ) ) return $ret;
         }
         return $ret;
      } catch (\Exception $e) {
		 throw new EVy( $this->position.": ".$e->getMessage(), $e->getCode(), $e );
      }
   }

   function __toString() {
      $ret = "";
      foreach ($this->stms as $s)
         $ret .= "$s\n";
      return $ret;
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
