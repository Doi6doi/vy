<?php

namespace vy;

class Member
   implements Expr
{

   const
      COUNT = "count";
	
   protected $base;
   protected $field;
	
   function __construct( Expr $base, $field ) {
	  $this->base = $base;
	  $this->field = $field;
   }
	
	function run( RunCtx $ctx ) {
	   $val = $this->base->run( $ctx );
	   if ( is_object( $val ))
	      return $val->member( $this->field );
	   else if ( is_array( $val ))
	      return $this->arrayMember( $val );
	   else
	      throw new EVy("Unknown member: $val".".".$this->field );
	}
	
	function call( RunCtx $ctx, array $args ) {
	   return $this->run( $ctx )->call( $ctx, $args );
	}	
	
	function __toString() {
	   return "<".$this->base.".".$this->field.">";
	}
	
	/// tömb mezője
	function arrayMember( $arr ) {
	   if ( self::COUNT == $this->field )
	      return count( $arr );
	   else
	      throw new EVy("Unknown array member: ".$this->field );	
    }		
	
}
