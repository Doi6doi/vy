<?php

namespace vy;

/// documentation creator
class MakeDox 
   extends MakeImport
{
	
   const
      DOX = "Dox";	
	
	protected $dox;
	
   function __construct( $owner ) {
	   parent::__construct( $owner, self::DOX );
	   Autoload::addPath( __DIR__."/../vydox" );
	   $this->dox = new Dox();
      $this->addFuncs( ["build","get","set","read","write"] );
   }
	
   /// generate output from input (read+write)
   function build( $dst, $src ) {
      $this->log( Make::INFO, "Building -> $dst" );
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
   function set( $fld, $val ) {
      $this->dox->set( $fld, $val );
   }
   
   /// write an output file
   function write( $dst=null ) {
      if ( $dst )
         $this->log( Make::INFO, "Writing $dst" );
      return $this->dox->write( $dst );
   }
   
   /// kimeneti típus beállítása
   function outType( $x ) {
      $this->dox->outType($x);
   }
   
   
}
