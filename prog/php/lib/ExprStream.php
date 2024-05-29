<?php

namespace vy;

class ExprStream
   extends Stream
{

   protected $blocks;
   protected $stacks;
   
   function __construct( $filename ) {
	  parent::__construct( $filename );
	  $this->blocks = [];
	  $this->stacks = [];
   }

   function push( Block $b, $newStack ) {
	  $this->blocks [] = $b;
	  if ( $newStack )
	     $this->stacks [] = new Stack( $b );
   } 

   function pop( $withStack ) {
	  array_pop( $this->blocks );
	  if ( $withStack )
	     array_pop( $this->stacks );
   }

   function top() {
	  return end( $this->blocks );
   }

   function stack() {
	  return end( $this->stacks );
   }

   function readExpr() {
	  return $this->stack()->readExpr( $this );
   }

   function readStm() {
	  $this->readWS();
	  $ret = null;
	  $n = $this->next();
	  $top = $this->top();
	  $kind = $top->kind();
	  if ( Block::COND == $kind ) {
		 if ( Given::GIVEN == $n )
		    $ret = new Given( $top );
	  }
	  $semi = false;
	  switch( $n ) {
		 case StmCase::CASE: $ret = new StmCase( $top ); break;
		 case StmIf::IF: $ret = new StmIf( $top ); break;
		 case StmFor::FOR: $ret = new StmFor( $top ); break;
		 case StmForeach::FOREACH: $ret = new StmForeach( $top ); break;
		 case StmReturn::RETURN: 
		    $ret = new StmReturn(); 
		    $semi = true;
		 break;
		 case StmThrow::THROW:
		    $ret = new StmThrow();
		    $semi = true;
		 break;
		 default: $semi = true;
	  }
	  if ( $ret )
         $ret->read( $this );
         else $ret = $this->readExpr();
      if ( $semi )
         $this->readToken(";");
	  return $ret;
   }

}

