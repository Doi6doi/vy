<?php

namespace vy;

class MakeImport
   extends ExprCtxForward
   implements Expr, ExprCtx
{
	
	protected $names;
	
	static function load( $owner, $name ) {
      throw new EVy("Unknown import: $name");
   }
	
	protected $name;

	protected function __construct( $owner, $name ) {
	   parent::__construct( $owner );
	   $this->name = $name;
	   $this->names = [];
	}
	
    function run( RunCtx $ctx ) { return $this; }
	
    function start() { }	

    function names() { return $this->names; }
	
	function __toString() { return $this->name; }
	
	function member( $field, $check ) {
	   if ( array_key_exists( $field, $this->names ))
	      return $this->names[$field];
      if ( $check ) {
	      throw new EVy(sprintf("Unknown member: %s.%s",
	         $this->name, $field ));
      }
      return null;
	}
	
	protected function add( $name, $val ) {
	   if ( array_key_exists( $name, $this->names ))
	      throw new EVy("Duplicate name: $name");
	   $this->names[$name] = $val;
	}
	
   /// függvények hozzáadása
   protected function addFuncs( $arr ) {
	  foreach ( $arr as $a )
	     $this->addFunc( $a );
   }
	
   /// egy függvény hozzáadása
   function addFunc( $name ) {
      $f = new MakeFunc( $this );
      $f->setCall( $name, [$this,$name] );
	   $this->add( $name, $f );
	   return $f;
   }
	
   /// naplózás	
   protected function log( $lvl, $msg ) {
	   $this->owner->log( $lvl, $msg );
   }
}
