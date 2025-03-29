<?php

namespace vy;

class ToolDebug extends PToolChoice {
   
   const
      GDB = "gdb";
   
   /// debugger gyártása
   static function create( $kind=null ) {
      if ( ! $kind )
         $kind = self::defaultKind();
      switch ($kind) {
         case self::GDB: return new ToolGdb();
         default: throw new EVy("Unknown debugger: $kind");
      }
   }

   /// alap típus a rendszerben
   static function defaultKind() { return self::GDB; }
   
   
   function __construct() {
	   parent::__construct();
      $this->setChoice( null );
 	   $this->addFuncs( ["debug"] );
   }

   /// debugger beállítása
   function setChoice( $v ) {
      PToolChoice::setChoice( self::create( $v ));
   }
   
}
