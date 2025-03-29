<?php

namespace vy;

/// C fordító
class PToolC extends PToolCompiler {
   
   const
      INCDIR = "incDir";

   static function cLiteral( $s, $wrap=null ) {
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

   function __construct() {
      parent::__construct();
      $this->set( self::INCDIR, [] );
	   $this->addFuncs( ["build", "depend", "libFile", "link",
         "literal", "loadDep", "objExt", "sourceRes"] );
   }
   
   /// függőségi fájl készítése
   function depend( $dst, $src ) {
      $this->mlog("depend", $dst, $src );
      $pp = new PToolCPreproc();
      $pp->setIncDir( $this->get( self::INCDIR ) );
      $pp->setSysInc( $this->envInc() );
      $pp->depend( $dst, $src, ".obj" );
   }

   /// függőségi fájl betöltése
   function loadDep( $fname ) {
      $this->mlog("loadDep",$fname);
      return (new PToolCPreproc())->loadDep( $fname );
   }

   /// könyvtár neve
   function libFile( $name ) {
      switch ( $sys = Tools::system() ) {
	      case Tools::WINDOWS: return "$name.dll";
		   case Tools::LINUX: return "lib$name.so";
		   default: throw new EVy("Unknown system: $sys");
      }
   }

   /// összeállítás
   function build( $dst, $src ) {
      throw Tools::notImpl("build");
   }

   /// linkelés
   function link( $dst, $src ) {
      throw Tools::notImpl("link");
   }
	   
   /// object kiterjesztés könyvtárban
   function objExt() {
      switch (Tools::system() ) {
		   case Tools::WINDOWS: return ".obj";
		   default: return ".o";
      }
   }

   /// erőforrás forrás készítése
   function sourceRes( $dst, $src, $name=null ) {
      $this->mlog("sourceRes", $dst, $src, $name );
      self::writeSourceRes( $dst, $src, $name );
   }

   function literal( $s ) {
      return self::cLiteral( $s, 72 );
   }

   /// környezeti INCLUDE változó könyvtárai
   protected function envInc() {
	  if ( $i = getenv( "INCLUDE" ))
	     return explode(";",$i);
	  return [];
   }

   protected function logFmt( $meth ) {
      switch ( $meth ) {
         case "build": return "Building -> %s";
         case "depend": return "Creating dependecies -> %s";
         case "link": return "Linking -> %s";
         case "loadDep": return "Loading dependecies: %s";
         case "sourceRes": return "Creating resource source -> %s";
         default: return parent::logFmt( $meth );
      }
   }

   protected function confKind( $fld ) {
      switch ($fld) {
         case self::INCDIR: return Configable::ARRAY;
         default: return parent::confKind( $fld );
      }
   }

   /// erőforrás karakter konvertálás
   protected static function writeResChar( OStream $os, $b, & $n ) {
      if ( 15 == $n % 16 )
         $os->write("\"\n   \"");
      if ( "\x20" <= $b && $b <= "\x7e" && "\\" != $b && "\"" != $b )
         $os->write( $b );
         else $os->writef( "\\%03o", ord($b) );
      ++$n;
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
