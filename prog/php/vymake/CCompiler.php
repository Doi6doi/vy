<?php

namespace vy;

/// C fordító
abstract class CCompiler 
   extends BaseCompiler
{
   
   /// fordító gyártása
   static function create( $kind=null ) {
      if ( ! $kind )
         $kind = self::defaultKind();
      switch ($kind) {
         case CppCompiler::GCC: return new Gcc(false);
         case CppCompiler::MSVC: return new Msvc(false);
         default: throw new EVy("Unknown compiler: $kind");
      }
   }

   /// alap típus a rendszerben
   static function defaultKind() {
      switch ( Tools::system() ) {
         case Tools::WINDOWS: return CppCompiler::MSVC;
         default: return CppCompiler::GCC;
      }
   }

   /// erőforrásfájl kiírása
   static function writeSourceRes( $dst, $src, $name ) {
      if ( ! $name )
         $name = pathinfo( $dst, PATHINFO_FILENAME );
      $bs = new BStream( $src );
      $os = new OStream( $dst );
      $n = 0;
      $os->writef( "char * %s_data = \"", $name ); 
      while ( ! $bs->eos() )
         self::writeResChar( $os, $bs->readByte(), $n );
      $os->writef("\";\n");
      $os->writel("unsigned %s_len = %d;", $name, $n );
      $os->close();
   }      

   /// erőforrás karakter konvertálás
   static function writeResChar( OStream $os, BStream $b, & $n ) {
      if ( 15 == $n % 16 )
         $os->write("\"\n   \"");
      if ( "\x20" <= $b && $b <= "\x7e" && "\\" != $b && "\"" != $b )
         $os->write( $b );
         else $os->writef( "\\%03o", ord($b) );
      ++$n;
   }
   
   /// include könyvtár
   protected $incDir;

   function __construct() {
      parent::__construct();
      $this->incDir = [];
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
