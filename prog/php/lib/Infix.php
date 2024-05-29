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
	  $ret = Oper::run( $this->op, $lv, $rv );
	  if ( Oper::isAssign( $this->op ))
	     $ctx->assign( $this->left, $ret );
	  return $ret;
   } 

   function __toString() {
      return sprintf("<%s%s%s>", $this->left, $this->op, $this->right );
   }
   
}
