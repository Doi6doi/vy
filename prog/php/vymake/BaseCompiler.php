<?php

namespace vy;

/// valamilyen fordító
abstract class BaseCompiler 
   extends CmdTool
{

   const
      CUSTOM = "custom";

   /// használható mezők
   const
      /// plusz argumentumok
      EARG = "earg",
      /// könyvtár fordítás
      LIBMODE = "libMode",
      /// debug mód
      DEBUG = "debug",
      /// lib könyvtár(ak)
      LIBDIR = "libDir",
      /// használt könyvtárak
      LIB = "lib",
      /// figyelmeztetések
      WARN = "warn";

   function __construct() {
      $this->set( self::LIBDIR, [] );
      $this->set( self::LIB, [] );
   }
   
   protected function confKind( $fld ) {
      switch ( $fld ) {
         case self::LIBMODE:
         case self::DEBUG:
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
   
   /// depend fájl készítés
   abstract function depend( $dst, $src );

   /// dep fájl felolvasása 
   abstract function loadDep( $fname );

   /// erőforrás forrássá alakítása
   abstract function sourceRes( $dst, $src, $name );

   /// object fájl készítése
   abstract function compile( $dst, $src );

   /// object fáljok linkelése futtahatóvá vagy könyvtárrá
   abstract function link( $dst, $src );

   /// fordítás és linkelés
   abstract function build( $dst, $src );

   /// tömbből összeállított argumentum
   protected function arrayArg( $arr, $pre ) {
      $ret = "";
      foreach ( $arr as $i )
         $ret .= " $pre$i";
      return $ret;
   }
   
}
