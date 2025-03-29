<?php

namespace vy;

/// valamilyen fordító
class PToolCompiler extends ToolCmd {

   /// használható mezők
   const
      /// plusz argumentumok
      EARG = "earg",
      /// debug mód
      DEBUG = "debug",
      /// használt könyvtárak
      LIB = "lib",
      /// lib könyvtár(ak)
      LIBDIR = "libDir",
      /// könyvtár fordítás
      LIBMODE = "libMode",
      /// kimeneti bináris verziója
      VER = "ver",
      /// figyelmeztetések
      WARN = "warn";

   function __construct() {
      parent::__construct();
	   $this->addFuncs( [ "compile" ] );
   }

   /// fordítás
   function compile( $dst, $src ) {
      throw Tools::notImpl( $this, "compile" );
   }

   /// tömbből összeállított argumentum
   protected function arrayArg( $arr, $pre ) {
      if ( ! $arr ) return "";
      $ret = "";
      foreach ( $arr as $i )
         $ret .= " $pre$i";
      return $ret;
   }
   
   protected function logFmt( $meth ) {
      switch ( $meth ) {
         case "compile": return "Compiling -> %s";
         default: return parent::logFmt( $meth );
      }
   }

   protected function confKind( $fld ) {
      switch ( $fld ) {
         case self::LIBMODE:
         case self::DEBUG:
         case self::VER:
         case self::WARN:
            return Configable::SCALAR;
         case self::LIBDIR:
         case self::LIB:
            return Configable::ARRAY;
         case self::EARG:
            return Configable::ANY;
         default:
            return parent::confKind( $fld );
      }
   }

   /// átmeneti fájl írása értékkel
   protected function addTemp( $val="" ) {
      $ret = Tools::temp( "bsc".getmypid()."_" );
      if ( "" < $val )
         Tools::saveFile( $ret, $val );
      $this->temps [] = $ret;
      return $ret;
   }
   
   /// átmeneti fájlok törlése
   protected function delTemps() {
      foreach ( $this->temps as $t )
         Tools::purge( $t );
      $this->temps = [];
   }

}
