<?php

namespace vy;

class ToolGit extends PToolChoice {

   /// git kliensek
   const
      CLI = "cli";
   
   /// konfig mezők
   const
      DEPTH = "depth";
   
   /// git eszköz
   static function create( $kind=null ) {
      if ( ! $kind ) 
         $kind = self::defaultKind();
      switch ($kind) {
         case self::CLI: return new ToolGitCli();
         default: throw new EVy("Unknown git: $kind");
      }
   }

   /// alap típus a rendszerben
   static function defaultKind() {  return self::CLI; }
   
   function __construct() {
	   parent::__construct();
      $this->set( self::CHOICE, null );
 	   $this->addFuncs( ["clone"] );
   }

   /// git beállítása
   function setChoice( $kind ) {
      PToolChoice::setChoice( self::create( $kind ));
   }

}
