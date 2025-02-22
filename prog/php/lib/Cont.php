<?php

namespace vy;

/// speciális visszatérési érték (return, break, continue, exception
class Cont {
	
	const
	   BREAK = "break",
	   CONTINUE = "continue",
	   RETURN = "return";
	
	const
	   BLOCK = "block",
	   FUNC = "func",
	   LOOP = "loop";
	
	/// befejező érték-e ez
	static function term( & $val, $mode ) {
	   if ( ! $isc = ($val instanceof Cont))
	      return false;
	   switch ( $mode ) {
		  case self::BLOCK:
		     return true;
		  case self::FUNC:
		     $val = $val->value();
		     return true;
		  case self::LOOP:
		     return self::CONTINUE != $val->kind();
		  default:
		     throw new EVy("Unknown Cont mode: $mode");
	   }
	}
	
	protected $kind;
	protected $value;
	
	function __construct( $kind, $value=null ) {
	   $this->kind = $kind;
	   $this->value = $value;
	}
	
	function kind() { return $this->kind; }
	
	function value() { return $this->value; }
	
}
