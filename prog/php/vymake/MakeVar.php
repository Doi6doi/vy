<?php

namespace vy;

/// make változó
class MakeVar 
   extends Vari
   implements RunCallable
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
	   if ( $v instanceof RunCallable )
	      return $v->call( $ctx, $args );
      else
	      throw new EVy("Cannot call variable ".$this->name );
   }
   
}
