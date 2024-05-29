<?php

namespace vy;

/// indexelt kifejezés
class Indexed
   implements Expr
{
	
	protected $base;
	protected $index;
	
    function __construct( $base, $index ) {
	   $this->base = $base;
	   $this->index = $index;
	}
	
    function run( RunCtx $ctx ) {
	   $val = $this->base->run( $ctx );
	   $idx = $this->index->run( $ctx );
	   if ( is_array( $val ))
	      return $val[ $idx ];
	   else
	      throw new EVy("Unknown index: $val[$idx]" );
	}
	
	function __toString() {
	   return "<".$this->base."[".$this->index."]>";
	}
	

}
