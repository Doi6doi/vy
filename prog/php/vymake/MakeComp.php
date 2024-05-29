<?php

namespace vy;

class MakeComp 
   extends MakeImport
{
	
   const
      COMP = "Comp";	
	
	protected $comp;
	
   function __construct( $owner ) {
	  parent::__construct( $owner, self::COMP );
	  Autoload::addPath( __DIR__."/../vyc" );
	  $this->comp = new Compiler();
      $this->addFuncs( ["compile", "setForce", "setRepo", "setRepr"] );
   }
	
   /// fordító futtatása
   function compile( $src, $dst ) {
	   $this->comp->addInput( $src );
	   $this->comp->addOutput( $dst );
	   $this->owner->log( Make::INFO, "Compiling $src -> $dst" );
	   $this->comp->run();
   }		
	
   /// felülírás beállítása
   function setForce( $x ) {
	  $this->comp->setForce( $x );
   }
	
	/// repository beállítása
	function setRepo( $x ) {
	   $r = $this->comp->repo();
	   $r->clear();
	   $r->add( $x );
	}
	
	/// reprezentáció beállítása
	function setRepr( $x ) {
	   $this->comp->setRepr( $x );
	}
	
}
