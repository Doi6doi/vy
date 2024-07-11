<?php

namespace vy;

/// C fordító
abstract class CCompiler 
   extends CmdTool
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
         case self::GCC: return new Gcc();
         case self::MSVC: return new Msvc();
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
   
   /// könyvtár mód
   protected $libMode;
   /// debug mód
   protected $debug;
   /// include könyvtár
   protected $incDir;
   /// lib könyvtár
   protected $libDir;
   /// használt könyvtárak
   protected $lib;
   /// figyelmeztetések
   protected $warn;

   function __construct() {
      $this->incDir = [];
      $this->libDir = [];
      $this->lib = [];
   }
   
   /// depend fájl készítés
   abstract function depend( $dst, $src );

   /// dep fájl felolvasása 
   abstract function loadDep( $fname );

   /// könyvtár mód 
   function setLibMode( $value ) { $this->libMode = $value; }
   
   /// könyvtár mód 
   function setWarning( $value ) { $this->warn = $value; }
   
   /// debug mód
   function setDebug( $value ) { $this->debug = $value; }

   /// erőforrás forrássá alakítása
   function sourceRes( $dst, $src, $name ) {
      $this->writeSourceRes( $dst, $src, $name );
   }

   /// object fájl készítése
   abstract function compile( $dst, $src );

   /// object fáljok linkelése futtahatóvá vagy könyvtárrá
   abstract function link( $dst, $src );

   /// include könyvtár beállítása
   function setIncDir( $dir ) { 
      $this->setArray( $this->incDir, $dir ); 
   }

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

   /// erőforrásfájl kiírása
   protected function writeSourceRes( $dst, $src, $name ) {
      if ( ! $name )
         $name = pathinfo( $dst, PATHINFO_FILENAME );
      $bs = new BStream( $src );
      $os = new OStream( $dst );
      $n = 0;
      $os->writef( "char * %s_data = \"", $name ); 
      while ( ! $bs->eos() )
         $this->writeResChar( $os, $bs->readByte(), $n );
      $os->writef("\";\n");
      $os->writel("unsigned %s_len = %d;", $name, $n );
      $os->close();
   }      

   /// erőforrás karakter konvertálás
   protected function writeResChar( $os, $b, & $n ) {
      if ( 15 == $n % 16 )
         $os->write("\"\n   \"");
      if ( "\x20" <= $b && $b <= "\x7e" && "\\" != $b && "\"" != $b )
         $os->write( $b );
         else $os->writef( "\\%03o", ord($b) );
      ++$n;
   }


   
}
