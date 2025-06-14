<?php

namespace vy;

class ToolJava extends PToolChoice {

   const
      OPENJDK = "OpenJDK";

   /// fordító gyártása
   static function create($kind) {
      if ( ! $kind )
         $kind = self::defaultKind();
      switch ($kind) {
         case self::OPENJDK: return new ToolOpenJdk();
         default: throw new EVy("Unknown java: $kind");
      }
   }

   /// alap típus a rendszerben
   static function defaultKind() {
      return self::OPENJDK;
   }

   function __construct() {
      parent::__construct();
      $this->set( self::CHOICE, null );
	   $this->addFuncs( ["compile", "jar",
         "objExt", "run"] );
   }

   function setChoice( $v ) {
      PToolChoice::setChoice( self::create( $v ));
   }

}
