<?php

namespace vy;

/// egy make cél
class MakeTarget 
   extends Block
   implements Expr
{
	
   /// cél neve
   protected $name;	
	
   function __construct( Make $owner ) {
      parent::__construct( $owner );
   }	
	
   function run( RunCtx $ctx ) {
      $ctx->push( $this->name );
      $ret = parent::run( $ctx );
      Cont::term( $ret, Cont::FUNC );
      $ctx->pop();
      return $ret;
   }
   
   function call( RunCtx $ctx, $args ) {
      return $this->run( $ctx );
   } 
   
   function name() { return $this->name; }	
	
   function kind() { return Block::BODY; }	
	
   /// cél olvasása	
   function read( ExprStream $s ) {
	  $s->readWS();
	  $this->name = $s->readIdent();
	  parent::read( $s );
   }	

   function __toString() { return "<".$this->name().">"; }

}
