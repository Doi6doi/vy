<?php

namespace vy;

/// for ciklus
class StmFor 
   extends Block
{
	
	const
	   FOR = "for";

    protected $init;
    protected $cond;
    protected $inc;
	
	function read( ExprStream $s ) {
      $this->position = $s->position();
	   $s->read( self::FOR );
	   $s->readWS();
	   $s->readToken("(");
	   $this->init = $s->readExpr();
	   $s->readToken(";");
	   $this->cond = $s->readExpr();
	   $s->readToken(";");
	   $this->inc = $s->readExpr();
	   $s->readToken(")");
      $this->readPart( $s );
	}
	
   function run( RunCtx $ctx ) {
	  $ctx->push( self::FOR, true );
	  try {
   	     $ret = $this->init->run( $ctx );
	     while ( $this->cond->run( $ctx )) {
		    $ret = parent::run( $ctx );
		    if ( Cont::term( $ret, Cont::LOOP ) ) return $ret;
		    $this->inc->run( $ctx );
         } 
      } finally {
         $ctx->pop();
      }
      return $ret;
   }
	
}
