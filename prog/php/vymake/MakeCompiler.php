<?php

namespace vy;

/// valamilyen fordító
class MakeCompiler extends MakeImport {

   protected $compiler;

   function __construct( $owner, $name ) {
      parent::__construct( $owner, $name );
	   $this->setCompiler( null );
	   $this->addFuncs( ["setCompiler","build",
         "compile","depend", "libFile","link",
         "loadDep","objExt", "setDebug","setLib","setLibDir",
         "setLibMode","setShow","setWarning","sourceRes"] );
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

   /// include könyvtár beállítása
   function setIncDir( $dir ) {
	  return $this->compiler->setIncDir( $dir );
   }

   /// debug mód beállítása
   function setDebug( $val ) {
	  return $this->compiler->setDebug( $val );
   }

   /// lib mód beállítása
   function setLibMode( $val ) {
	  return $this->compiler->setLibMode( $val );
   }

   /// lib könyvtár beállítása
   function setLibDir( $dir ) {
	  return $this->compiler->setLibDir( $dir );
   }

   /// használt könyvtárak beállítása
   function setLib( $dir ) {
	  return $this->compiler->setLib( $dir );
   }

   /// include könyvtár beállítása
   function setShow( $x ) {
	  return $this->compiler->setShow( $x );
   }

   /// warningok beállítása
   function setWarning( $x ) {
	  return $this->compiler->setWarning( $x );
   }

   /// fordító beállítása
   function setCompiler( $cmp ) {
	  $this->compiler = CCompiler::create( $cmp );
   }

   /// erőforrás készítése
   function sourceRes( $dst, $src, $name=null ) {
      $this->log( Make::INFO, "Creating resource source -> $dst" );
      return $this->compiler->sourceRes( $dst, $src, $name );
   }

}
