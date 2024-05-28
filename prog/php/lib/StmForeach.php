<?php

namespace vy;

class StmForeach 
   extends Block
{
	
	const
	   FOREACH = "foreach";

    protected $var;
    protected $parl;
    protected $range;
	
	function read( ExprStream $s ) {
	   $s->read( self::FOREACH );
	   $s->readWS();
	   $s->readToken("(");
	   $s->readWS();
	   $this->var = $s->readIdent();
	   $s->readWS();
	   if ( $s->readIf("|"))
	      $this->parl = true;
	   else if ( $s->readIf(":"))
	      $this->parl = false;
	   else
	      throw $s->notexp("separator");
	   $this->range = $s->readExpr();	
	   $s->readToken(")");
	   parent::read( $s );
	}
	
   function run( RunCtx $ctx ) {
	  $ctx->push( self::FOREACH );
	  $r = $this->range->run( $ctx );
	  if ( is_array( $r ) ) {
		 foreach ( $r as $i ) {
			$ctx->setVar( $this->var, $i );
			parent::run( $ctx );
	     }
	  }
   }
	
	
	
}
