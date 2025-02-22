<?php

namespace vy;

abstract class Git 
   extends CmdTool
{

   const
      CLI = "cli",
      CUSTOM = "custom";
   
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
      $this->show = true;
   }
   
}
