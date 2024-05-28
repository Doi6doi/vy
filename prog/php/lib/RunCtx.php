<?php

namespace vy;

class RunCtx {
	
	protected $frames;
	
	function __construct() {
	   $this->frames = [];
	   $this->push( "global" );
	}
	
	function push( $name ) {
	   $this->frames [] = new RunFrame( $name );
	}
	
	function top() {
	   if ( ! $this->frames )
	      throw new EVy("No top frame");
	   return end( $this->frames );
	}
	
	function pop() {
	   if ( 1 == count( $this->frames ))
	      throw new EVy("Cannot pop global frame");
	   array_pop( $this->frames );
	}
	
	function assign( $obj, $val ) {
	   if ( $obj instanceof GlobalVar )
	      $this->setGlobal( $obj->name(), $val );
	   else if ( $obj instanceof Vari )
	      $this->setVar( $obj->name(), $val ); 
	   else 
          throw new EVy("Cannot assign to ".Tools::withClass($obj));
	}
	
	function getGlobal( $name ) {
	   return $this->frames[0]->getVar( $name );
	}
	
	function setGlobal( $name, $val ) {
	   $this->frames[0]->setVar( $name, $val );
	}	
	
	function getVar( $name ) {
	   return $this->top()->getVar( $name );
	}
	
	function setVar( $name, $val ) {
	   $this->top()->setVar( $name, $val );
	}
	
	
}
