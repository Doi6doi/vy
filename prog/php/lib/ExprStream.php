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
	  switch( $n ) {
		 case StmIf::IF: $ret = new IfCond( $kind ); break;
		 case StmReturn::RETURN: $ret = new StmReturn(); break;
	  }
	  if ( $ret )
         $ret->read( $this );
         else $ret = $this->readExpr();
      $this->readToken(";");
	  return $ret;
   }

}

