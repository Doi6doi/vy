<?php

namespace vy;

/// Make ben levő függvény
class MakeFunc 
   extends ExprCtxForward
   implements Expr    
{
	
   protected $name;	
   protected $call;
   protected $sign;
   protected $body;
	
   function __construct( $owner ) {
	  parent::__construct( $owner );
   }

   function name() { return $this->name; }

   /// hívható beállítása
   function setCall( $name, $call ) {
	  $this->name = $name;
	  $this->call = $call;
   }
   
   /// olvasás
   function read( ExprStream $s ) {
	  $s->readWS();
	  $this->name = $s->readIdent();
	  $this->sign = new Sign( $this );
	  $this->sign->read( $s );
	  $this->body = new Block( $this, Block::BODY );
	  $this->body->read( $s );
   }
   
   function call( RunCtx $ctx, $args ) {
	  $ctx->push( $this->name );
	  $this->sign->setArgs( $ctx, $args );
	  $ret = $this->body->run( $ctx );
      $ctx->pop();
      return $ret;
   } 

   function run( RunCtx $ctx ) {
	  return $this->body->run( $ctx );
   }
   
   function __toString() { return $this->name; }
   
} 	
	
	
