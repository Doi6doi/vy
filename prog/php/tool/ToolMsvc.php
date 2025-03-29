<?php

namespace vy;

class ToolMsvc extends PToolCpp {

   function compile( $dst, $src ) {
      $this->mlog("compile",$dst,$src);
      $this->set( self::EXE, "CL.exe" );
      $this->exec(
         $this->warnArg(),
         $this->debugArg(),
         $this->incDirArg(),
         "/c",
         "/Fo".$this->esc($dst),
         $this->esc($src)
      );
   }
   
   function build( $dst, $src ) {
      $this->mlog("build",$dst,$src);
      $this->set( self::EXE, "CL.exe" );
      $this->exec(
         $this->warnArg(),
         $this->debugArg(),
         $this->incDirArg(),
         "/Fe".$this->esc($dst),
         $this->esc($src),
         $this->libDirArg(),
	      $this->libArg()
      );
   }
   
   function link( $dst, $src ) {
      $this->mlog("link",$dst,$src);
      $this->set( self::EXE, "LINK.exe" );
	   $this->exec(
	      $this->modeLinkArg(),
	      "/OUT:".$this->esc($dst),
	      $this->esc($src),
	      $this->libDirArg(),
	      $this->libArg()
	   );
   }
   
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
	
}
