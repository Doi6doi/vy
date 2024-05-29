<?php

require_once( __DIR__."/../lib/autoload.php" );

vy\Autoload::addPath( __DIR__ );

/// vy make eszköz
class VyMake {

   const
      MAKEVY = "Make.vy";

   protected $file;
   protected $targets;

   function __construct() {
      $this->targets = [];
   }

   function run( $argv ) {
      $this->getParams( $argv );
      $make = vy\Make::load( $this->file );
      $make->run( $this->targets );
   }

   /// paraméterek olvasása
   function getParams( $argv ) {
      try {
         $at = 1;
         while ( $this->getParam( $argv, $at ))
            ;
         if ( ! $this->file )
            $this->file = self::MAKEVY;
      } catch (Exception $e) {
         $this->usage($e->getMessage());
      }
   }

   /// egy paraméter olvasása
   function getParam( $argv, & $i ) {
      if ( $i >= count($argv))
         return false;
      switch ($a = $argv[$i]) {
         default: 
            $this->targets [] = $a;
            ++$i;
            return true;
      }
   }

   /// használat
   function usage( $msg ) {
      $ret = [
         "",
         "Usage: php vymake.php [<target>..]",
         "",
         ""
      ];
      fprintf( STDERR, implode("\n",$ret) );
      if ( $msg )
         fprintf( STDERR, "$msg\n" );
      exit(1);
   }


}

(new VyMake())->run( $argv );
