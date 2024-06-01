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
	   $s->readWS();
	   if ( ";" != $s->next() )
  	      $this->expr = $s->readExpr();
  	}
  	
  	function run( RunCtx $ctx ) {
	   $ret = null;
	   if ( $e = $this->expr )
	      $ret = $e->run( $ctx );
	   return new Cont( Cont::RETURN, $ret );
	}
	
}
