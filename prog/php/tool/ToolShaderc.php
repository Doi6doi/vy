<?php

namespace vy;

class ToolShaderc extends PToolC {

   function __construct() {
      parent::__construct();
      $this->set( self::EXE, "glslc" );
	   $this->delFuncs( ["build", "libFile", "link",
         "literal", "objExt", "sourceRes"] );
   }

   function depend( $dst, $src ) {
      $this->mlog("depend",$dst,$src);
      $this->exec(
         $this->incDirArg(),
         "-MM ".$this->esc($src),
         ">".$this->esc( $dst )
      );
   }
   
   function compile( $dst, $src ) {
      $this->mlog("compile",$dst,$src);
      $this->exec(
         $this->eArg(),
         "-c",
         $this->warnArg(),
         $this->debugArg(),
         $this->incDirArg(),
         "-o ".$this->esc( $dst ),
         $this->esc( $src )
      );
   }
   
   /// debug argumentum
   function debugArg() {
      return $this->get( self::DEBUG ) ? "-g": "";
   }

   /// warning argumentum
   function warnArg() {
      return $this->get( self::WARN ) ? "-Werror": "";
   }
   
   /// include könyvtár parancssori argumentum
   function incDirArg() { 
      return $this->arrayArg( $this->get( self::INCDIR ), "-I " ); 
   }
      
   /// extra argumentumok
   function eArg() {
      $a = $this->get( self::EARG );
      if ( is_array( $a ))
         $a = implode(" ",$a);
      return $a;
   }

   function loadDep( $fname ) {
      $this->mlog("loadDep",$fname);
      return (new PToolCPreproc())->loadDep( $fname );
   }
}
