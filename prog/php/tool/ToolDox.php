<?php

namespace vy;

/// documentation creator
class ToolDox extends PToolBase {
	
	protected $dox;
	
   function __construct() {
	   parent::__construct();
	   Autoload::addPath( __DIR__."/../vydox" );
	   $this->dox = new Dox();
      $this->addFuncs( ["build","setVar",
         "read","write","writePart"] );
   }
	
   /// generate output from input (read+write)
   function build( $dst, $src ) {
      $this->mlog( "build", $dst, $src );
      $this->dox->read( $src );
      $this->dox->write( $dst );
   }
   
   /// read an input file
   function read( $src ) {
      $this->dox->read( $src );
   }

   /// get config value
   function get( $fld ) {
      return $this->dox->get( $fld );
   }
   
   /// set config value
   function set( $fld, $val=true ) {
      $this->dox->set( $fld, $val );
   }
   
   /// set custom value
   function setVar( $fld, $val ) {
      $this->dox->setVar( $fld, $val );
   }
   
   /// write an output file
   function write( $dst=null ) {
      if ( $dst )
         $this->mlog( "write", $dst );
      return $this->dox->write( $dst );
   }
   
   /// write a part to file 
   function writePart( $part, $dst = null ) {
      return $this->dox->writePart( $part, $dst );
   }
   
   protected function logFmt( $meth ) {
      switch ( $meth ) {
         case "build": return "Building -> %s";
         case "write": return "Writing -> %s";
         default: return parent::logFmt( $meth );
      }
   }
      
}
