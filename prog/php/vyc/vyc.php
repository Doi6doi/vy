<?php

require_once( "../lib/autoload.php" );

/// vy fordító
class Vyc {

   /// bemeneti fájl
   protected $infile;
   /// stream
   protected $stream;
   /// kimeneti fájl
   protected $outfile;
   /// a feldolgozott struktúra
   protected $obj;

   function run( $argv ) {
      $this->getParams( $argv );
      $this->read();
      $this->transform();
      $this->write();
   }

   /// bemeneti fájl olvasása
   function read() {
      $s = $this->stream = new VyStream( $this->infile );
      $s->readWS();
      switch ( $k = $s->next() ) {
         case VyInterface::INTERFACE: $this->obj = new VyInterface(); break;
         default: throw new Exception("Unknown file: $k" );
      }
      $this->obj->read( $s );
   }

   /// paraméterek olvasása
   function getParams( $argv ) {
      try {
         $at = 1;
         while ( $this->getParam( $argv, $at ))
            ;
      } catch (Exception $e) {
         $this->usage($e->getMessage());
      }
   }

   /// egy paraméter olvasása
   function getParam( $argv, & $i ) {
      if ( $i >= count($argv))
         return false;
      switch ($argv[$i]) {
         case "-i": return $this->getParFile( $argv, $i, $this->infile );
         case "-o": return $this->getParFile( $argv, $i, $this->outfile );
         default: throw new Exception("Unknown parameter: $argv[$i]");
      }
   }

   /// fájl paraméter olvasása
   function getParFile( $argv, & $i, & $fname ) {
      ++$i;
      if ( $i >= count($argv))
         throw new Exception("File name expected");
      $fname = $argv[$i++];
      return true;
   }

}

(new Vyc())->run( $argv );

