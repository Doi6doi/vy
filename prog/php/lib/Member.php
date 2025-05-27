<?php

namespace vy;

class Member
   implements Expr, RunCallable
{

   const
      COUNT = "count";
	
   protected $base;
   protected $field;
   protected $check;
	
   function __construct( Expr $base, $field, $check ) {
	  $this->base = $base;
	  $this->field = $field;
     $this->check = $check;
   }
	
	function run( RunCtx $ctx ) {
	   $val = $this->base->run( $ctx );
      if ( is_object( $val ))
	      return $val->member( $this->field, $this->check );
	   else if ( is_array( $val ))
	      return $this->arrayMember( $val );
	   else if ( $this->check )
	      throw new EVy("Unknown member: $val".".".$this->field );
      else
         return null;
	}
	
	function call( RunCtx $ctx, $args ) {
	   return $this->run($ctx)->call( $ctx, $args );
	}	
	
	function __toString() {
	   return "<".$this->base.".".$this->field.">";
	}
	
	/// tömb mezője
	function arrayMember( $arr ) {
	   if ( self::COUNT == $this->field )
	      return count( $arr );
	   else if ( array_key_exists( $this->field, $arr ))
         return $arr[$this->field];
      else if ( $this->check )
	      throw new EVy("Unknown array member: ".$this->field );	
      else
         return null;
    }		
	
}
