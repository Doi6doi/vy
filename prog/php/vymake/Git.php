<?php

namespace vy;

abstract class Git 
   extends CmdTool
{

   const
      CLI = "cli",
      CUSTOM = "custom";
   
   /// konfig mezők
   const
      DEPTH = "depth";
   
   /// git gyártása
   static function create( $kind=null ) {
      if ( ! $kind )
         $kind = self::defaultKind();
      switch ($kind) {
         case self::CLI: return new GitCli();
         default: throw new EVy("Unknown git: $kind");
      }
   }

   /// alap típus a rendszerben
   static function defaultKind() { 
      return self::CLI; 
   }
   
   abstract function clone( $url );

   function __construct() {
      $this->set( self::SHOW, true );
   }
   
   /// változó fajtája
   protected function confKind( $fld ) {
      switch ( $fld ) {
         case self::DEPTH: return Configable::SCALAR;
         default: return parent::confKind( $fld );
      }
   }
   
}
