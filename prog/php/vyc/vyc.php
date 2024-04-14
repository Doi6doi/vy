<?php

require_once( "../lib/autoload.php" );

/// vy fordító
class VyC {

   /// fordító
   protected $comp;

   function __construct() {
      $this->comp = new vy\Compiler();
   }

   function run( $argv ) {
      $this->getParams( $argv );
      $this->comp->run();
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
         case "-r": return $this->getParRepo( $argv, $i );
         case "-i": return $this->getParInput( $argv, $i );
         case "-t": return $this->getParType( $argv, $i );
         case "-o": return $this->getParOutput( $argv, $i );
         default: throw new Exception("Unknown parameter: $argv[$i]");
      }
   }

   /// következő paraméter
   function nextPar( $argv, & $i, $kind ) {
      if ( count($argv) <= ++$i )
         throw new Exception("Missing $kind");
      return $argv[$i++];
   }

   /// repo paraméter olvasása
   function getParRepo( $argv, & $i ) {
      $this->comp->repo()->add( $this->nextPar( $argv, $i, "repo uri" ) );
      return true;
   }

   /// input paraméter olvasása
   function getParInput( $argv, & $i ) {
      $this->comp->addInput( $this->nextPar( $argv, $i, "input" ));
      return true;
   }

   /// output paraméter olvasása
   function getParOutput( $argv, & $i ) {
      $this->comp->addOutput( $this->nextPar( $argv, $i, "output" ));
      return true;
   }

   function getParType( $argv, & $i ) {
      $this->comp->setTypeMap( $this->nextPar( $argv, $i, "typemap" ));
      return true;
   }

   /// használat
   function usage( $msg ) {
      $ret = [
         "",
         "Usage: php vyc.php <options>",
         "",
         "Options:",
         "   -r <path>: add <path> as repository",
         "   -i <item>: add <item> as input",
         "   -t <typemap>: set <typemap> as type mapping",
         "   -o <filename>: add <filename> as output",
         "",
         ""
      ];
      fprintf( STDERR, implode("\n",$ret) );
      if ( $msg )
         fprintf( STDERR, "$msg\n" );
      exit(1);
   }

}

(new VyC())->run( $argv );

