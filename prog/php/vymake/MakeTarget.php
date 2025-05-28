<?php

namespace vy;

/// egy make cél
class MakeTarget 
   extends Block
   implements Expr, RunCallable
{
	
   /// cél neve
   protected $name;	
	
   function __construct( Make $owner ) {
      parent::__construct( $owner );
   }	
	
   function run( RunCtx $ctx ) { return $this; }
   
   function call( RunCtx $ctx, $args ) {
      $ctx->push( $this->name );
      $ret = parent::run( $ctx );
      Cont::term( $ret, Cont::FUNC );
      $ctx->pop();
      return $ret;
   } 
   
   function name() { return $this->name; }	
	
   function blockKind() { return Block::BODY; }	
	
   /// cél olvasása	
   function read( ExprStream $s ) {
	  $s->readWS();
	  $this->name = $s->readIdent();
	  parent::read( $s );
   }	

   function __toString() { return "<".$this->name().">"; }

}
