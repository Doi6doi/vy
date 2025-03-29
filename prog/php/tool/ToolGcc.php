<?php

namespace vy;

class ToolGcc extends PToolCpp {

   function set( $fld, $val=null ) {
      parent::set( $fld, $val );
      if ( self::PP == $fld )
         $this->set( self::EXE, $val ? "g++" : "gcc" );
   }
   
   function depend( $dst, $src ) {
      $this->mlog( "depend", $dst, $src );
      $this->exec(
         $this->incDirArg(),
         "-MM ".$this->esc($src),
         ">".$this->esc( $dst )
      );
   }
   
   function link( $dst, $src ) {
      $this->mlog( "link", $dst, $src );
      $this->exec(
         $this->eArg(),
         $this->modeLinkArg(),
         "-o ".$this->esc($dst),
         $this->esc($src),
         $this->libDirArg(),
         $this->libArg()
      );
   }
 
   function build( $dst, $src ) {
      $this->mlog( "build", $dst, $src );
      $this->exec(
         $this->eArg(),
         $this->warnArg(),
         $this->debugArg(),
         $this->modeCompArg(),
         $this->incDirArg(),
         $this->modeLinkArg(),
         "-o ".$this->esc( $dst ),
         $this->esc($src),
         $this->libDirArg(),
         $this->libArg()
      );
   }
   
   function compile( $dst, $src ) {
      $this->mlog( "compile", $dst, $src );
      $this->exec(
         $this->eArg(),
         "-c",
         $this->warnArg(),
         $this->debugArg(),
         $this->modeCompArg(),
         $this->incDirArg(),
         "-o ".$this->esc( $dst ),
         $this->esc( $src )
      );
   }
   
   /// mód argumentum linkelésnél
   function modeLinkArg() {
      return $this->get( self::LIBMODE ) ? "-shared" : "";
   }
   
   /// debug argumentum
   function debugArg() {
      return $this->get( self::DEBUG ) ? "-g": "";
   }

   /// warning argumentum
   function warnArg() {
      return $this->get( self::WARN ) ? "-w -Werror": "";
   }
   
   /// mód argumentum fordításnál
   function modeCompArg() {
      return $this->get( self::LIBMODE ) ? "-fPIC" : "";
   }
   
   /// include könyvtár parancssori argumentum
   function incDirArg() { 
      return $this->arrayArg( $this->get( self::INCDIR ), "-I " ); 
   }
      
   /// include könyvtár parancssori argumentum
   function libDirArg() {
      return $this->arrayArg( $this->get( self::LIBDIR ), "-L " );
   }

   /// extra argumentumok
   function eArg() {
      $a = $this->get( self::EARG );
      if ( is_array( $a ))
         $a = implode(" ",$a);
      return $a;
   }

   /// használt könyvtár parancssori argumentum
   function libArg() {
      return $this->arrayArg( $this->get( self::LIB ), "-l" );
   }      
   
}
