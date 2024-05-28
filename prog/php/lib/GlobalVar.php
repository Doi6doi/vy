<?php

namespace vy;

class GlobalVar
   implements Vari
{
	
	protected $name;

    function __construct( $name ) {
	   $this->name = $name;
	}
	
	function name() { return $this->name; }
	
	function run( RunCtx $ctx ) {
	   return $ctx->getGlobal( $this->name );
	}	

    function __toString() {
	   return "<$".$this->name.">";
	}
}
