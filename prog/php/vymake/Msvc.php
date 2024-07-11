<?php

namespace vy;

class Msvc extends CCompiler {

   protected $exe;
	
   function depend( $dst, $src ) {
	  $pp = new CPreproc();
	  $pp->setIncDir( $this->incDir );
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
   
   function link( $dst, $src ) {
	  $this->exe = "LINK.exe";
      $this->run( "%s /OUT:%s %s %s %s", $this->modeLinkArg(), 
         $this->esc($dst), $this->esc($src), $this->libDirArg(), 
         $this->libArg() );
   }
   
   function executable() { return $this->exe; }

   /// warning argumentum
   protected function warnArg() {
      return $this->warn ? "/W4": "";
   }
   
   /// linkelési mód argumentum
   protected function modeLinkArg() {
      return $this->libMode ? "/DLL" : "";
   }
	
   /// linkelési könyvtár argumentum
   protected function libDirArg() {
	  return $this->arrayArg( $this->libDir, "/LIBPATH:" );
   }
	
   /// debug argumentum
   function debugArg() {
      return $this->debug ? "/Z7": "";
   }
	
   /// linkelt könyvtárak
   function libArg() {
	  return $this->arrayArg( $this->lib, "" );
   }	
	
   /// include könyvtár parancssori argumentum
   function incDirArg() { 
      return $this->arrayArg( $this->incDir, "/I " ); 
   }
   
   /// környezeti INCLUDE változó könyvtárai
   protected function envInc() {
	  if ( $i = getenv( "INCLUDE" ))
	     return explode(";",$i);
	  return [];
   }
	
	
}
