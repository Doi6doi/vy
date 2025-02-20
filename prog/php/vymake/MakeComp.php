<?php

namespace vy;

/// vy compiler
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
      $this->addFuncs( ["compile", "setForce", "setMap",
         "setRepo", "setReprs"] );
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
	
   /// megfeleltetés beállítása
   function setMap( $x ) {
      $this->comp->setTypeMap( $x );
   }
   
	/// repository beállítása
	function setRepo( $x ) {
	   $r = $this->comp->repo();
	   $r->clear();
	   $r->add( $x );
	}
   
   /// reprezentációk beállítása
   function setReprs( $x ) {
      $this->comp->setReprs( $x );
   }
	
}
