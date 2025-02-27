<?php

namespace vy;

class Msvc extends CppCompiler {

   protected $exe;
	
   function depend( $dst, $src ) {
      $pp = new CPreproc();
      $pp->setIncDir( $this->get( self::INCDIR ) );
      $pp->setSysInc( $this->envInc() );
      $pp->depend( $dst, $src, ".obj" );
   }

   function loadDep( $fname ) {
	  $pp = new CPreproc();
	  $ret = $pp->loadDep( $fname );
      return $ret;	  
   }

   function compile( $dst, $src ) {
	   $this->exe = "CL.exe";
       $this->run( "%s %s %s /c /Fo%s %s", $this->warnArg(), 
          $this->debugArg(), $this->incDirArg(), 
          $this->esc($dst), $this->esc($src)
       );
   }
   
   function build( $dst, $src ) {
 	  $this->exe = "CL.exe";
      $this->runArgs([
         $this->warnArg(),
         $this->debugArg(),
         $this->incDirArg(),
         "/Fe".$this->esc($dst),
         $this->esc($src),
         $this->libDirArg(),
	     $this->libArg()
      ]);
   }
   
   function link( $dst, $src ) {
	   $this->exe = "LINK.exe";
	   $this->runArgs([
	      $this->modeLinkArg(),
	      "/OUT:".$this->esc($dst),
	      $this->esc($src),
	      $this->libDirArg(),
	      $this->libArg()
	   ]);
   }
   
   function executable() { return $this->exe; }

   /// warning argumentum
   protected function warnArg() {
      return $this->get( self::WARN ) ? "/W4": "";
   }
   
   /// linkelési mód argumentum
   protected function modeLinkArg() {
      return $this->get( self::LIBMODE ) ? "/DLL" : "";
   }
	
   /// linkelési könyvtár argumentum
   protected function libDirArg() {
	  return $this->arrayArg( $this->get( self::LIBDIR ), "/LIBPATH:" );
   }
	
   /// debug argumentum
   function debugArg() {
      return $this->get( self::DEBUG ) ? "/Z7": "";
   }
	
   /// linkelt könyvtárak
   function libArg() {
	  return $this->arrayArg( $this->get( self::LIB ), "" );
   }	
	
   /// include könyvtár parancssori argumentum
   function incDirArg() { 
      return $this->arrayArg( $this->get( self::INCDIR ), "/I " ); 
   }
   
   /// környezeti INCLUDE változó könyvtárai
   protected function envInc() {
	  if ( $i = getenv( "INCLUDE" ))
	     return explode(";",$i);
	  return [];
   }
	
	
}
