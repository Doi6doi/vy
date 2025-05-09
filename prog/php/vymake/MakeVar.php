<?php

namespace vy;

/// make változó
class MakeVar 
   extends Vari
{

   protected $value;
   
   function setValue( $x ) { $this->value = $x; }
   
   function run( RunCtx $ctx ) {
      if ( $this->value )
         return $this->value;
         else return parent::run( $ctx );
   }
   
   function call( RunCtx $ctx, $args ) {
      $v = $this->value;
	   if ( $v instanceof MakeFunc 
        || $v instanceof MakeTarget
      )
	     return $v->call( $ctx, $args );
	  else
	     throw new EVy("Cannot call ".$this->name );
   }
   
}
