<?php

namespace vy;

/// infix kifejezés
class Infix
   implements Expr
{

   protected $op;
   protected $left;
   protected $right;

   function __construct( $op, Expr $left, Expr $right ) {
      $this->op = $op;
      $this->left = $left;
      $this->right = $right;
   }

   function run( RunCtx $ctx ) {
	  $lv = null;
	  if ( ":=" != $this->op )
	     $lv = $this->left->run( $ctx );
	  $rv = $this->right->run( $ctx );
	  $ret = $this->runOp( $lv, $rv );
	  if ( Oper::isAssign( $this->op ))
	     $ctx->assign( $this->left, $ret );
	  return $ret;
   } 

   function __toString() {
      return sprintf("<%s%s%s>", $this->left, $this->op, $this->right );
   }

   protected function runOp( $lv, $rv ) {
	  switch ($this->op) {
		 case ":=": return $rv; 
		 case "+": 
		 case "+=": 
		    return $this->plus( $lv, $rv );
		 default: throw new EVy("Cannot run operator ".$this->op);
	  }
   }
   
   /// összeadás
   protected function plus( $lv, $rv ) {
	  if ( is_array($lv) ) {
		 if ( ! is_array( $rv ))
		    $rv = [$rv];
		 return array_merge( $lv, $rv ); 
	  } else
	     return $lv + $rv;
   }
   
}
