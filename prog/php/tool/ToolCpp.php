<?php

namespace vy;

/// Cpp fordító modul
class ToolCpp extends ToolC {

   const
      MSVC = "msvc",
      GCC = "gcc",
      CLANG = "clang";

   /// fordító gyártása
   static function create( $kind=null ) {
      if ( ! $kind )
         $kind = self::defaultKind();
      switch ($kind) {
         case self::CLANG: return new ToolClang(true);
         case self::GCC: return new ToolGcc(true);
         case self::MSVC: return new ToolMsvc(true);
         default: throw new EVy("Unknown compiler: $kind");
      }
   }

   /// alap típus a rendszerben
   static function defaultKind() {
      switch ( Tools::system() ) {
         case Tools::WINDOWS: return self::MSVC;
         default: return self::GCC;
      }
   }

   function setChoice( $v ) {
      PToolChoice::setChoice( self::create( $v ));
   }

}
