<?php

namespace vy;

/// Glsl fordító
class ToolGlsl extends PToolChoice
{
   
   const
      SHADERC = "shaderc";
   
   /// fordító gyártása
   static function create( $kind=null ) {
      if ( ! $kind )
         $kind = self::defaultKind();
      switch ($kind) {
         case self::SHADERC: return new ToolShaderc();
         default: throw new EVy("Unknown compiler: $kind");
      }
   }

   /// alap típus a rendszerben
   static function defaultKind() {
      return self::SHADERC;
   }
   
   function __construct() {
      parent::__construct();
      $this->set( self::CHOICE, null );
	   $this->addFuncs( [ "compile", "depend", "loadDep" ] );
   }

   function setChoice( $v ) {
      PToolChoice::setChoice( self::create( $v ));
   }
   
   
}
