<?php

namespace vy;

class MakeDebug extends MakeImportCmd {
   
   const
      DEBUG = "Debug";

   protected $debugger;
   
   function __construct( $owner ) {
	   parent::__construct( $owner, self::DEBUG );
      $this->setDebugger( null );
 	   $this->addFuncs( ["debug"] );
   }

   function cmd() { return $this->debugger; }

   /// debugger beállítása
   function setDebugger( $dbg ) {
	  $this->debugger = Debugger::create( $dbg );
   }

   function debug( $prg ) {
      $this->debugger->debug( $prg );
   }
   
   
   
   
}
