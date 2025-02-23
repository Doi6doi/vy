<?php

namespace vy;

/// valamilyen fordító
class MakeCompiler extends MakeImport {

   const
      COMPILER = "compiler";

   protected $compiler;

   function __construct( $owner, $name ) {
      parent::__construct( $owner, $name );
	   $this->set( self::COMPILER, null );
	   $this->addFuncs( ["build",
         "compile","depend", "get", "libFile","link",
         "loadDep","objExt", "set", "sourceRes"] );
   }

   /// fordítás
   function compile( $dst, $src ) {
	   $this->log( Make::INFO, "Compiling -> $dst" );
	   $this->compiler->compile( $dst, $src );
   }

   /// összeállítás
   function build( $dst, $src ) {
	  $this->log( Make::INFO, "Building -> $dst" );
	  $this->compiler->build( $dst, $src );
   }

   /// könyvtár neve
   function libFile( $name ) {
	  switch ( $sys = Tools::system() ) {
		 case Tools::WINDOWS: return "$name.dll";
		 case Tools::LINUX: return "lib$name.so";
		 default: throw new EVy("Unknown system: $sys");
      }
   }
 
   /// object kiterjesztés könyvtárban
   function objExt() {
	  switch (Tools::system() ) {
		 case Tools::WINDOWS: return ".obj";
		 default: return ".o";
      }
   }

   /// depend fájl készítése
   function depend( $dst, $src ) {
	  $this->log( Make::INFO, "Creating dependency file -> $dst" );
	  $this->compiler->depend( $dst, $src );
   }

   /// linkelés
   function link( $dst, $src ) {
	  $this->log( Make::INFO, "Linking -> $dst" );
	  $this->compiler->link( $dst, $src );
   }
	   
   /// dep fájl beolvasása
   function loadDep( $fname ) {
	  $this->log( Make::INFO, "Loading dependencies: $fname" );
	  return $this->compiler->loadDep( $fname );
   }

   /// változó értéke
   function get( $fld ) {
      return $this->compiler->get( $fld );
   }

   /// változó beállítása
   function set( $fld, $val ) {
      if ( self::COMPILER == $fld )
         $this->setCompiler( $val );
         else $this->compiler->set( $fld, $val );
   }

   /// fordító beállítása
   function setCompiler( $val ) {
      throw new EVy("Unknown compiler: $val");
   }

   /// erőforrás készítése
   function sourceRes( $dst, $src, $name=null ) {
      $this->log( Make::INFO, "Creating resource source -> $dst" );
      return $this->compiler->sourceRes( $dst, $src, $name );
   }

}
