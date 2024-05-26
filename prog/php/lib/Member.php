<?php

namespace vy;

class Member
   implements Expr
{
	
	protected $base;
	protected $field;
	
	function __construct( Expr $base, $field ) {
	   $this->base = $base;
	   $this->field = $field;
	}
	
	function run( RunCtx $ctx ) {
	   $val = $this->base->run( $ctx );
	   return $val->member( $this->field );
	}
	
	function call( RunCtx $ctx, array $args ) {
	   return $this->run( $ctx )->call( $ctx, $args );
	}	
	
	function __toString() {
	   return "<".$this->base.".".$this->field.">";
	}
	
}
