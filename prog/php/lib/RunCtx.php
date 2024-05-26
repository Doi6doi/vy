<?php

namespace vy;

class RunCtx {
	
	protected $frames;
	
	function __construct() {
	   $this->frames = [];
	}
	
	function push( $name ) {
	   $this->frames [] = new RunFrame( $name );
	}
	
}
