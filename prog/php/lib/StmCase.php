<?php

namespace vy;

class StmCase 
   extends ExprCtxForward
   implements Stm
{
	
   const
      CASE = "case";	

   protected $cond;
   protected $branches;
   
   function __construct( Block $owner ) {
	  parent::__construct( $owner );
	  $this->branches = [];
   }
      
   /// if olvasása
   function read( ExprStream $s ) {
      $s->readWS();
      $s->readToken( self::CASE );
      $s->readWS();
      $s->readToken("(");
      $this->cond = $s->readExpr();
      $s->readToken(")");
      $s->readWS();
      $s->readToken("{");
      while ( $this->readBranch( $s ))
         ;
      $s->readToken("}");
   }

   function run( RunCtx $ctx ) {
	  $v = $this->cond->run( $ctx );
	  foreach ( $this->branches as $b ) {
	     if ( $b->matches( $v ))
	        return $b->run( $ctx );
	  }
	  return null;
   }

   /// egy case ág olvasása
   protected function readBranch( ExprStream $s ) {
	  $s->readWS();
	  if ( "}" == $s->next() )
	     return false;
	  $ret = new StmBranch( $this );
	  $ret->read( $s );
	  $this->branches [] = $ret;
	  return true;
   }   
}
