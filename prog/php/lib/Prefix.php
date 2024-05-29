<?php

namespace vy;

/// prefix kifejezés
class Prefix
   implements Expr
{

   protected $op;
   protected $body;

   function __construct( $op, Expr $body ) {
      $this->op = $op;
      $this->body = $body;
   }

   function run( RunCtx $ctx ) {
	  $ret = $this->body->run( $ctx );
	  $ret = Oper::run( $this->op, $ret );
	  if ( Oper::isAssign( $this->op ))
	     $ctx->assign( $this->body, $ret );
	  return $ret;
   } 

   function __toString() {
      return sprintf("<%s%s>", $this->op, $this->body );
   }

}
