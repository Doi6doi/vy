<?php

namespace vy;

abstract class Debugger 
   extends CmdTool
{

   const
      GDB = "gdb",
      CUSTOM = "custom";
   
   /// debugger gyártása
   static function create( $kind=null ) {
      if ( ! $kind )
         $kind = self::defaultKind();
      switch ($kind) {
         case self::GDB: return new Gdb();
         default: throw new EVy("Unknown debugger: $kind");
      }
   }

   /// alap típus a rendszerben
   static function defaultKind() { 
      return self::GDB; 
   }
   
   abstract function debug( $prg );

   function __construct() {
      $this->set( self::SHOW, true );
   }
      
   
}
