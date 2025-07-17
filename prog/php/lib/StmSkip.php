<?php

namespace vy;

class StmSkip
   implements Stm
{
	function __toString() {
	   return "<skip>";
	}
	
	function read( Stream $s ) { }
  	
  	function run( RunCtx $ctx ) { }
	
}
