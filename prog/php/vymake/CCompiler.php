<?php

namespace vy;

/// C fordító
abstract class CCompiler 
   extends BaseCompiler
{
   
   const
      INCDIR = "incDir";
   
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

   static function literal( $s, $wrap=null ) {
      $ret = "";
      while ( 0 < strlen( $s ) ) {
         if ( $ret )
            $ret .= "\n";
         $ret .= self::literalPart( $s, $wrap );
      }
      return $ret;
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
   
   function __construct() {
      parent::__construct();
      $this->set( self::INCDIR, [] );
   }
   
   /// erőforrás forrássá alakítása
   function sourceRes( $dst, $src, $name ) {
      self::writeSourceRes( $dst, $src, $name );
   }

   protected function confKind( $fld ) {
      switch ($fld) {
         case self::INCDIR: return Configable::ARRAY;
         default: return parent::confKind( $fld );
      }
   }
   
   /// a literal egy része
   protected static function literalPart( & $s, $wrap ) {
      $l = strlen( $s );
      if ( $wrap && $wrap < $l )
         $l = $wrap;
      if ( (false !== $i = strpos( $s, "\n" ))
         && ($i+1 < $l))
         $l = $i+1;
      $ret = '"';
      for ($i=0; $i<$l; ++$i)
         $ret .= self::literalChar( $s[$i] );
      $ret .= '"';
      $s = substr( $s, $l );
      return $ret;
   }
   
   /// egy karakter literálban
   protected static function literalChar( $c ) {
      switch ( $c ) {
         case "\\": return "\\\\";
         case '"': return "\\\"";
         case "\n": return "\\n";
         case "\t": return "\\t";
         case "\r": return "\\r";
         case "\f": return "\\f";
         case "\0": return "\\0";
         default: return $c;
      }
   }
         
   
   
}
