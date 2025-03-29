<?php

namespace vy;

class ToolC extends PToolChoice {

   /// fordító gyártása
   static function create( $kind=null ) {
      if ( ! $kind )
         $kind = self::defaultKind();
      switch ($kind) {
         case ToolCpp::GCC: return new ToolGcc(false);
         case ToolCpp::MSVC: return new ToolMsvc(false);
         case ToolCpp::CLANG: return new ToolClang(false);
         default: throw new EVy("Unknown compiler: $kind");
      }
   }

   /// alap típus a rendszerben
   static function defaultKind() {
      switch ( Tools::system() ) {
         case Tools::WINDOWS: return ToolCpp::MSVC;
         default: return ToolCpp::GCC;
      }
   }

   function __construct() {
      parent::__construct();
      $this->set( self::CHOICE, null );
	   $this->addFuncs( ["build", "compile", "depend", "libFile", "link",
         "literal", "loadDep", "objExt", "sourceRes"] );
   }

   function setChoice( $v ) {
      PToolChoice::setChoice( self::create( $v ));
   }

}
