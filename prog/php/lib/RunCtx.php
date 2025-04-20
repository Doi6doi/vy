<?php

namespace vy;

class RunCtx {
	
	protected $globl;
   protected $thisObj;
	protected $frames;
	
	function __construct() {
	   $this->globl = new RunFrame("global");
	   $this->frames = [];
	}

   /// globális környzet
   function globl() { return $this->globl; }
   
   /// aktuális objektum környezet
   function thisObj() { return $this->thisObj; }
	
	function push( $name ) {
	   $this->frames [] = new RunFrame( $name );
	}
	
	function top() {
	   if ( ! $this->frames )
	      throw new EVy("No top frame");
	   return end( $this->frames );
	}
	
	function pop() {
	   if ( ! $this->frames )
	      throw new EVy("No frames to pop");
	   array_pop( $this->frames );
	}
	
	function assign( $obj, $val ) {
	   if ( $obj instanceof GlobalVar )
	      $this->globl->setVar( $obj->name(), $val );
	   else if ( $obj instanceof Vari )
	      $this->setVar( $obj->name(), $val ); 
	   else 
          throw new EVy("Cannot assign to ".Tools::withClass($obj));
	}
	
	function getVar( $name ) {
	   if ( $f = $this->find( $name ))	
	      return $f->getVar( $name );
	      else throw new EVy("Unknown variable: $name");
	}
	
	function setVar( $name, $val ) {
	   if ( ! $f = $this->find( $name ))
	      $f = $this->top();
	   $f->setVar( $name, $val );
	}
	
   /// változót tartalmazó blokk keresése
   protected function find( $name ) {
	  for ($i=count($this->frames)-1; 0<=$i; --$i) {
	     $f = $this->frames[$i];
	     if ( $f->has( $name ))
		    return $f;
	  }
	  return false;
   }
	       
	
}
