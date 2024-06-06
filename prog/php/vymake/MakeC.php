<?php

namespace vy;

class MakeC extends MakeImport {

   const
      C = "C";

   protected $compiler;

   function __construct( $owner ) {
	  parent::__construct( $owner, self::C );
	  $this->setCompiler( null );
	  $this->addFuncs( ["setCompiler","compile","depend",
	     "libFile","linkLib","linkPrg", "loadDep","objExt",
	     "setIncDir","setLib","setLibDir","setShow"] );
   }

   /// fordítás
   function compile( $dst, $src ) {
	  $this->log( Make::INFO, "Compiling -> $dst" );
	  $this->compiler->compile( $dst, $src );
   }

   /// könyvtár neve
   function libFile( $name ) {
	  switch ( $sys = Tools::system() ) {
		 case Tools::WINDOWS: return "$name.dll";
		 case Tools::LINUX: return "lib$name.so";
		 default: throw new EVy("Unknown system: $sys");
      }
   }
 
   /// object fájlok a könyvtárban
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

   /// könyvtár linkelése
   function linkLib( $dst, $src ) {
	  $this->log( Make::INFO, "Linking library -> $dst" );
	  $this->compiler->linkLib( $dst, $src );
   }
	   
   /// program linkelése
   function linkPrg( $dst, $src ) {
	  $this->log( Make::INFO, "Linking executable -> $dst" );
	  $this->compiler->linkPrg( $dst, $src );
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

   /// fordító beállítása
   function setCompiler( $cmp ) {
	  $this->compiler = CCompiler::create( $cmp );
   }

}
