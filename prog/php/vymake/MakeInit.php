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

   function blockKind() { return Block::BODY; }

   function call( RunCtx $ctx, $args ) {
      if ( ! $path = Tools::g( $args, 0 ))
         throw new EVy("Missing init argument");
      if ( ! file_exists( $path ))
         $path = Tools::path($path, Make::MAKEVY);
      $m = Make::load( $path );
      if ( ! $i = $m->init() )
         throw new EVy("Does not have init: $path");
      $dir = dirname( $path );
      $save = getcwd();
      Tools::chdir( $dir );
      $i->run( $ctx );
      Tools::chdir( $save );
   }

   function run( RunCtx $ctx ) { }

   function __toString() { return "<".$this->name().">"; }

} 	
	
	
