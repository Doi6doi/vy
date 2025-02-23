<?php

namespace vy;

/// Core.init hívás
class MakeInit 
   extends ExprCtxForward
   implements Expr
{
	
   const
      INIT = "init";
   
   function name() { return self::INIT; }

   function kind() { return Block::BODY; }

   function call( RunCtx $ctx, $args ) {
      if ( ! $path = Tools::g( $args, 0 ))
         throw new EVy("Missing init argument");
      if ( ! file_exists( $path ))
         $dir = Tools::path($path, Make::MAKEVY);
      $m = Make::load( $path );
      if ( ! $i = $m->init() )
         throw new EVy("Does not have init: $path");
      $i->run( $ctx );
   }

   function run( RunCtx $ctx ) { }

   function __toString() { return "<".$this->name().">"; }

} 	
	
	
