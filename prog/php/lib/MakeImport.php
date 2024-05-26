<?php

namespace vy;

class MakeImport
   extends ExprCtxForward
   implements Expr, ExprCtx
{
	
	protected $names;
	
	static function load( $owner, $name ) {
	   switch ( $name ) {
		  case MakeC::C: return new MakeC( $owner );
		  default: throw new EVy("Unknown import: $name");
	   }
	}
	
	protected $name;

	protected function __construct( $owner, $name ) {
	   parent::__construct( $owner );
	   $this->name = $name;
	   $this->names = [];
	}
	
    function run( RunCtx $ctx ) { return $this; }
	
	function __toString() { return $this->name; }
	
	function member( $field ) {
	   if ( ! array_key_exists( $field, $this->names ))
	      throw new EVy(sprintf("Unknown member: %s.%s",
	         $this->name, $field ));
	   return $this->names[$field];
	}
	
	protected function add( $name, $val ) {
	   if ( array_key_exists( $name, $this->names ))
	      throw new EVy("Duplicate name: $name");
	   $this->names[$name] = $val;
	}
	
	protected function addFunc( $name ) {
	   $f = new MakeFunc( $this );
	   $f->setCall( $name, [$this,$name] );
	   $this->add( $name, $f );
	}
	   
}
