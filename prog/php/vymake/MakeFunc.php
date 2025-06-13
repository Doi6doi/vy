<?php

namespace vy;

/// Make ben levő függvény
class MakeFunc 
   extends ExprCtxForward
   implements Expr, RunCallable
{
	
   protected $name;	
   protected $call;
   protected $sign;
   protected $body;
	
   function name() { return $this->name; }

   function blockKind() { return Block::BODY; }

   /// hívható beállítása
   function setCall( $name, $call ) {
	  $this->name = $name;
	  $this->call = $call;
   }
   
   /// olvasás
   function read( ExprStream $s ) {
	  $s->readWS();
	  $this->name = $s->readIdent();
	  $this->sign = new Sign( $this, false );
	  $this->sign->read( $s );
	  $this->body = new Block( $this );
	  $this->body->read( $s );
   }
   
   function call( RunCtx $ctx, $args ) {
	   if ( $this->call )
		   return call_user_func_array( $this->call, $args );
      else if ( $this->body ) {
	      $ctx->push( $this->name, false );
   	   $this->setArgs( $ctx, $args );
         $ret = $this->body->run( $ctx );
         Cont::term( $ret, Cont::FUNC );
         $ctx->pop();
         return $ret;
      } else
         throw new EVy("Cannot run ".$this->name);
   } 

   function run( RunCtx $ctx ) {
	   return $this->body->run( $ctx );
   }

   function __toString() { return $this->name; }

   /// aktuális paraméterek beállítása
   protected function setArgs( RunCtx $ctx, array $args ) {
	  if ( $this->sign )
	     return  $this->sign->setArgs( $ctx, $args );
	  for( $i=0; $i<count($args); ++$i)
	     $ctx->setVar( Arg::ARG.$i, $args[$i] );
   }
   
} 	
	
	
