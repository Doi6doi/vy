<?php

namespace vy;

/// C++ fordító
abstract class CppCompiler 
   extends BaseCompiler
{
   
   const
      MSVC = "msvc",
      GCC = "gcc",
      CLANG = "clang",
      CUSTOM = "custom";
   
   /// fordító gyártása
   static function create( $kind=null ) {
      if ( ! $kind )
         $kind = self::defaultKind();
      switch ($kind) {
         case self::GCC: return new Gcc(true);
         case self::MSVC: return new Msvc(true);
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
   
   /// c++ mód
   protected $pp;
   /// include könyvtár
   protected $incDir;

   function __construct($pp) {
      parent::__construct();
      $this->incDir = [];
      $this->pp = $pp;
   }
   
   /// erőforrás forrássá alakítása
   function sourceRes( $dst, $src, $name ) {
      self::writeSourceRes( $dst, $src, $name );
   }

   /// include könyvtár beállítása
   function setIncDir( $dir ) { 
      $this->setArray( $this->incDir, $dir ); 
   }
   
}
