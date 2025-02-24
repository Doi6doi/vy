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

   /// temporary files deleted after command run
   protected $temps;

   function __construct() {
      $this->set( self::LIBDIR, [] );
      $this->set( self::LIB, [] );
      $this->temps = [];
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
   
   /// futtatás után ideiglenes fájlok törlése
   protected function run() {
      $ret = parent::run( ...func_get_args());
      $this->delTemps();
      return $ret;
   }
   
   /// futtatás argumentumokkal
   protected function runArgs( array $args ) {
      return $this->run( "%s", implode(" ",$args));
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
