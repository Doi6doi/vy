<?php

namespace vy;

class StmReturn 
   implements Stm
{
	const
	   RETURN = "return";
	
	protected $expr;
	
	function __toString() {
	   return self::RETURN." ".$this->expr;
	}
	
	function read( Stream $s ) {
	   $s->readToken(self::RETURN);
  	   $this->expr = $s->readExpr();
  	}
  	
  	function run( RunCtx $ctx ) {
	   return $this->expr->run( $ctx );
	}
	
}
