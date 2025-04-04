<?php

namespace vy;

/// függvényhívás
class Call
   implements Expr
{

   /// a hívott függvény
   protected $target;
   /// argumentumok
   protected $args;

   function __construct( Expr $target, $args ) {
      $this->target = $target;
      $this->args = [];
      if ( $args ) {
         if ( $args instanceof Tuple ) {
            foreach ( $args->items() as $i )
               $this->add( $i );
         } else
            $this->add( $args );
      }
   }

   function run( RunCtx $ctx ) {
	  $vals = [];
	  foreach( $this->args as $a )
	     $vals [] = $a->run( $ctx );
	  $t = $this->target;
	  if ( ! method_exists( $t, "call" ))
	     throw new EVy("Cannot call ".Tools::withClass($t));
	  return $t->call( $ctx, $vals );
   }

   function add( Expr $e ) {
      $this->args [] = $e;
   }

   function __toString() {
      return "<".$this->target."(".implode(",",$this->args).")>";
   }

}
