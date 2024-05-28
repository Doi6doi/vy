<?php

namespace vy;

/// make változó
class MakeVar 
   implements Vari
{

   protected $name;
   protected $value;
   
   function __construct( $name ) {
	  $this->name = $name;
   }
   
   function name() { return $this->name; }
   
   function setValue( $x ) { $this->value = $x; }
   
   function call( RunCtx $ctx, $args ) {
	  if ( $this->value instanceof MakeFunc )
	     return $this->value->call( $ctx, $args );
	  else
	     throw new EVy("Cannot call ".$this->name );
   }
   
   function run( RunCtx $ctx ) {
	  return $ctx->getVar( $this->name );
   }
   
   function __toString() { return $this->name; }	
	
}
