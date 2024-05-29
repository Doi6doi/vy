<?php

require_once( __DIR__."/../lib/autoload.php" );

vy\Autoload::addPath( __DIR__ );

/// vy make eszköz
class VyMake {

   const
      MAKEVY = "Make.vy";

   protected $file;
   protected $target;

   function run( $argv ) {
      $this->getParams( $argv );
      $make = vy\Make::load( $this->file );
      $make->run( $this->target );
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
      switch ($argv[$i]) {
         default: throw new Exception("Unknown parameter: $argv[$i]");
      }
   }

}

(new VyMake())->run( $argv );
