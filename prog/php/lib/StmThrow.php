<?php

namespace vy;

/// kivétel dobás
class StmThrow
   implements Stm
{

   const
      THROW = "throw";

   protected $obj;

   function read( ExprStream $s ) {
	  $s->readToken( self::THROW );
	  $this->obj = $s->readExpr();
   }
   
   function run( RunCtx $ctx ) {
	  $val = $this->obj->run( $ctx );
	  throw new EVy( $val );
   }
   
   
}
