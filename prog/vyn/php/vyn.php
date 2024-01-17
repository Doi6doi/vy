<?php

require_once("autoload.php");

/// Vy futtató

class Vyn {

   protected $filename;

   function run( $argv ) {
      try {
         $this->getParams( $argv );
         $b = new VyBin();
         $b->load( $this->filename );
         $c = new VyContext();
         $b->run( $c );
      } catch (EParam $e) {
         $this->usage($e);
      }
   }

   function getParams( $argv ) {
     $this->filename = Tools::g( $argv, 1 );
     if ( ! $this->filename )
        throw new EParam( "Missing file name" );
   }

   function usage($err) {
      $ret = [
         "Usage: php vyn.php <filename.vyb>",
         "",
         "Loads a vy binary file and executes it",
         ""
     ];
     fwrite( STDERR, implode("\n", $ret));
     if ( $err )
        fwrite( STDERR, "\n".$err->getMessage()."\n\n" );
      exit(1);
   }


}


(new Vyn())->run( $argv );
