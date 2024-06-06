<?php

namespace vy;

/// C fordító
abstract class CCompiler {
   
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
   
   /// parancsok kiírása
   protected $show;
   /// include könyvtár
   protected $incDir;
   /// lib könyvtár
   protected $libDir;
   /// használt könyvtárak
   protected $lib;

   function __construct() {
      $this->incDir = [];
      $this->libDir = [];
      $this->lib = [];
   }
   
   /// depend fájl készítés
   abstract function depend( $dst, $src );

   /// object fáljok linkelése könyvtárrá
   abstract function linkLib( $dst, $src );

   /// object fájlok linkelése programmá
   abstract function linkPrg( $dst, $src );

   /// dep fájl felolvasása 
   abstract function loadDep( $fname );

   /// object fájl készítése
   abstract function compile( $dst, $src );

   /// a futtatható program
   abstract function executable();

   function setShow( $value ) { $this->show = $value; }

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

   /// fordító futtatása
   protected function run($fmt) {
      $args = call_user_func_array( "sprintf", func_get_args() );
      return $this->exec( $this->executable(), $args );
   }

   /// külső program futtatása
   protected function exec( $prg, $args ) {
      $out = [];
      $cmd = sprintf( "%s %s", $this->esc($prg), $args );
      if ( $this->show ) {
         print( "$cmd\n" );
         $r = passthru( $cmd, $rv );
         $outs = "";
      } else {
         $cmd .= " 2>&1";
         $r = exec( $cmd, $out, $rv );
         $outs = implode("\n",$out);
      }
      if ( (false === $r) || (0 != $rv) )
         throw new EVy("Exec error: $rv $outs");
      return $outs;
   }
   
   /// parancssori escape
   protected function esc( $x ) {
      if ( ! $x )
         return "";
      if ( ! is_array( $x ))
         $x = [$x];
      $ret = "";
      foreach ( $x as $i )
         $ret .= " ".escapeshellarg($i);
      return trim($ret);
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
