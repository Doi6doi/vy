<?php

namespace vy;

class GlobalVar
   extends Vari
{
	
	function run( RunCtx $ctx ) {
	   return $ctx->globl()->getVar( $this->name );
	}	

}
