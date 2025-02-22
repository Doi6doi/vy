<?php

namespace vy;

/// valamilyen fordító
abstract class BaseCompiler 
   extends CmdTool
{

   const
      CUSTOM = "custom";

   /// könyvtár mód
   protected $libMode;
   /// debug mód
   protected $debug;
   /// lib könyvtár
   protected $libDir;
   /// használt könyvtárak
   protected $lib;
   /// figyelmeztetések
   protected $warn;

   function __construct() {
      $this->libDir = [];
      $this->lib = [];
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

   /// könyvtár mód 
   function setLibMode( $value ) { $this->libMode = $value; }
   
   /// könyvtár mód 
   function setWarning( $value ) { $this->warn = $value; }
   
   /// debug mód
   function setDebug( $value ) { $this->debug = $value; }

   /// lib könyvtár beállítása
   function setLibDir( $dir ) { 
      $this->setArray( $this->libDir, $dir ); 
   }

   /// használt könyvtárak beállítása
   function setLib( $dir ) { 
      $this->setArray( $this->lib, $dir ); 
   }

   /// egy tömb adatainak beállítása
   protected function setArray( & $arr, $x ) {
      if ( ! $x )
         $arr = [];
      else if ( is_array( $x ))
         $arr = $x;
      else
         $arr = [$x];
   }

   /// tömbből összeállított argumentum
   protected function arrayArg( $arr, $pre ) {
      $ret = "";
      foreach ( $arr as $i )
         $ret .= " $pre$i";
      return $ret;
   }
   
}
