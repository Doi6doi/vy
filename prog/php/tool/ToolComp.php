<?php

namespace vy;

/// vy compiler
class ToolComp extends PToolBase {
	
   const
      FORCE = "force",
      MAP = "map",
      REPO = "repo",
      REPRS = "reprs";   
   
	protected $comp;
	
   function __construct() {
	   parent::__construct();
	   Autoload::addPath( __DIR__."/../vyc" );
	   $this->comp = new Compiler();
      $this->addFuncs( ["compile"] );
/*      , "setForce", "setMap",
         "setRepo", "setReprs"] );
         */
   }
	
   function set( $fld, $val=true ) {
      parent::set( $fld, $val );
      if ( is_array($fld) ) return;
      $v = $this->get( $fld );
      $c = $this->comp;
      switch ($fld) {
         case self::FORCE: $c->setForce($v); break;
         case self::MAP: $c->setTypeMap($v); break;
         case self::REPO:
            $c->repo()->clear();
            $c->repo()->addRepo($v);
         break;
         case self::REPRS: $c->setReprs($v); break;
      }
   }

   /// fordító futtatása
   function compile( $dst, $src ) {
	   $this->mlog( "compile", $dst, $src );
	   $this->comp->addInput( $src );
	   $this->comp->addOutput( $dst );
	   $this->comp->run();
   }		
   
   protected function confKind( $fld ) {
      switch ( $fld ) {
         case self::FORCE: return self::BOOL;
         case self::MAP: return self::ANY;
         case self::REPO: return self::ARRAY;
         case self::REPRS: return self::ARRAY;
         default: return parent::confKind($fld);
      }
   }

   protected function logFmt( $meth ) {
      switch ( $meth ) {
         case "compile": return "Compiling %s";
         default: return parent::logFmt( $meth );
      }
   }
	
}
