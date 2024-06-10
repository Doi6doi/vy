<?php

namespace vy;

class MakeDebug extends MakeImport {
   
   const
      DEBUG = "Debug";

   protected $debugger;
   
   function __construct( $owner ) {
	   parent::__construct( $owner, self::DEBUG );
      $this->setDebugger( null );
 	   $this->addFuncs( ["debug"] );
   }

   /// debugger beállítása
   function setDebugger( $dbg ) {
	  $this->debugger = Debugger::create( $dbg );
   }

   function debug( $prg ) {
      $this->debugger->debug( $prg );
   }
   
   
   
   
}
