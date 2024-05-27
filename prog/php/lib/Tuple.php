<?php

namespace vy;

/// vesszős lista
class Tuple
   implements Expr
{
   protected $items;

   function __construct() {
      $this->items = [];
   }

   function items() { return $this->items; }

   function run( RunCtx $ctx ) {
	  $ret = [];
	  foreach ($this->items as $i)
	     $ret [] = $i->run( $ctx );
	  return $ret;
   }

   function add( Expr $e ) {
      $this->items [] = $e;
   }

   function __toString() {
      return "<".implode(",",$this->items).">";
   }

}
